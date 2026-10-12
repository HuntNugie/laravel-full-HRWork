<?php

namespace App\Jobs;

use App\Ai\Agents\CVAnalyzer as CVAnalyzerAgent;
use App\Models\CvAnalysisRun;
use App\Models\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class AnalyzeCvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public int $tries = 1;

    public int $maxExceptions = 1;

    public function __construct(public string $analysisRunId)
    {
        $this->onQueue('cv-analysis');
    }

    public function handle(): void
    {
        $run = CvAnalysisRun::query()->find($this->analysisRunId);

        if (! $run) {
            return;
        }

        if (in_array($run->status, ['completed', 'failed', 'cancelled'], true)) {
            $this->deleteSourceCv($run);

            if ($run->status === 'cancelled') {
                $run->delete();
            }

            return;
        }

        $disk = Storage::disk($run->file_disk);

        if (! $disk->exists($run->file_path)) {
            $this->markFailed($run, 'File CV tidak ditemukan saat job dijalankan.');

            return;
        }

        $run->forceFill([
            'status' => 'processing',
            'started_at' => now(),
            'finished_at' => null,
            'error_message' => null,
        ])->save();

        try {
            $path = $disk->path($run->file_path);
            $cvText = $this->extractCvText(
                $path,
                (string) pathinfo($run->original_filename, PATHINFO_EXTENSION),
            );

            if (mb_strlen(trim($cvText)) < 80) {
                throw new \RuntimeException(
                    'Isi CV tidak cukup untuk dianalisis. Pastikan dokumen memiliki teks yang dapat dibaca.'
                );
            }

            // Prefer the position snapshot captured when HR submitted the form.
            // This keeps queued analysis tied to the selected position even if the
            // position record changes before the worker starts processing.
            $positionContext = $run->position_context;

            // Backward compatibility for jobs queued before position_context existed.
            if (! $positionContext && $run->position_id) {
                $position = Position::query()
                    ->with('jobdesk')
                    ->where('is_active', 'active')
                    ->find($run->position_id);

                if (! $position) {
                    throw new \RuntimeException('Posisi target tidak ditemukan atau sudah tidak aktif.');
                }

                $positionContext = [
                    'name' => $position->name,
                    'description' => $position->description,
                    'jobdesk' => $position->jobdesk
                        ->pluck('jobdesk')
                        ->filter()
                        ->values()
                        ->all(),
                ];

                // Persist the fallback snapshot too, so the selected target can be
                // shown consistently for jobs queued before this migration.
                $run->forceFill(['position_context' => $positionContext])->save();
            }

            $prompt = $this->buildPrompt(
                cvText: Str::limit($cvText, 60000, ''),
                positionContext: $positionContext,
                companyCriteria: trim((string) $run->company_criteria),
            );

            // Use the same streaming response path as AI Assistant. The provider accepts
            // streaming requests, while its non-streaming response can be incompatible
            // with Laravel AI's strict OpenAI-compatible response parser.
            $response = CVAnalyzerAgent::make()->stream(
                $prompt,
                provider: '9router',
                model: config('ai.providers.9router.models.text.default'),
                timeout: 540,
            );

            // Drain the stream in the queue worker and then parse the complete JSON.
            foreach ($response as $event) {
                // The CV Analyzer UI polls the stored job result; it does not stream to the browser.
            }

            $structured = $this->decodeAnalysisResponse($response->text ?? '');

            $requiredKeys = [
                'candidate',
                'position_alignment',
                'company_alignment',
                'strengths',
                'gaps',
                'verification_items',
                'interview_questions',
                'feedback',
            ];

            $missingKeys = array_values(array_diff($requiredKeys, array_keys($structured)));

            if ($missingKeys !== []) {
                throw new \RuntimeException(
                    'AI tidak mengembalikan JSON analisis CV yang lengkap. Bagian yang tidak tersedia: '
                    . implode(', ', $missingKeys)
                );
            }

            $result = [];

            foreach ($requiredKeys as $key) {
                $result[$key] = $structured[$key];
            }

            $run->refresh();

            // The user may cancel while the provider is processing the request.
            // Clean up the uploaded CV before making a terminal state visible to polling.
            if ($run->status !== 'cancelled') {
                $this->deleteSourceCv($run);

                $run->forceFill([
                    'status' => 'completed',
                    'result' => $result,
                    'error_message' => null,
                    'finished_at' => now(),
                ])->save();
            } else {
                $this->deleteSourceCv($run);
                $run->delete();
            }
        } catch (Throwable $exception) {
            report($exception);
            $run->refresh();

            $this->deleteSourceCv($run);

            if ($run->status !== 'cancelled') {
                $this->markFailed($run, $this->safeErrorMessage($exception));
            } else {
                $run->delete();
            }
        }
    }

    public function failed(?Throwable $exception): void
    {
        $run = CvAnalysisRun::query()->find($this->analysisRunId);

        if (! $run || in_array($run->status, ['completed', 'cancelled'], true)) {
            return;
        }

        if ($exception) {
            report($exception);
        }

        $this->markFailed(
            $run,
            'Analisis CV gagal dijalankan oleh worker. Silakan coba kembali.'
        );

        $this->deleteSourceCv($run);
    }

    protected function markFailed(CvAnalysisRun $run, string $message): void
    {
        $run->refresh();

        if ($run->status === 'cancelled') {
            return;
        }

        $run->forceFill([
            'status' => 'failed',
            'error_message' => $message,
            'finished_at' => now(),
        ])->save();
    }

    protected function deleteSourceCv(CvAnalysisRun $run): void
    {
        try {
            Storage::disk($run->file_disk)->delete($run->file_path);
        } catch (Throwable $cleanupException) {
            report($cleanupException);
        }
    }

    protected function safeErrorMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();
        $lowerMessage = strtolower($message);

        if (
            str_contains($lowerMessage, 'timeout')
            || str_contains($lowerMessage, 'timed out')
            || str_contains($lowerMessage, 'curl error 28')
        ) {
            return 'Provider AI tidak merespons tepat waktu. Silakan coba lagi atau periksa status layanan AI.';
        }

        if (
            str_contains($lowerMessage, 'could not connect to ai provider')
            || str_contains($lowerMessage, 'connection refused')
            || str_contains($lowerMessage, 'could not resolve host')
        ) {
            return 'Tidak dapat terhubung ke provider AI. Periksa URL, API key, dan koneksi provider.';
        }

        if (
            str_contains($lowerMessage, 'pdftotext')
            || str_contains($message, 'PDF')
        ) {
            return 'PDF tidak dapat dibaca di server. Pastikan file PDF berisi teks dan utilitas pdftotext tersedia.';
        }

        if (str_contains($message, 'DOCX')) {
            return 'Dokumen DOCX tidak dapat dibaca. Pastikan file tidak rusak.';
        }

        if (
            str_contains($message, 'JSON')
            || str_contains($message, 'AI tidak mengembalikan')
        ) {
            return 'AI memberikan format hasil yang tidak sesuai. Silakan ulangi analisis.';
        }

        return 'Analisis CV gagal diproses. Periksa log aplikasi untuk detail teknis.';
    }

    protected function decodeAnalysisResponse(string $text): array
    {
        $text = trim($text);

        if ($text === '') {
            throw new \RuntimeException('AI mengembalikan response kosong.');
        }

        if (preg_match('/\x60\x60\x60(?:json)?\s*(.*?)\s*\x60\x60\x60/si', $text, $matches) === 1) {
            $text = trim($matches[1]);
        }

        $decoded = json_decode($text, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($text, $start, $end - $start + 1), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        throw new \RuntimeException('AI mengembalikan output yang bukan JSON valid.');
    }

    protected function buildPrompt(
        string $cvText,
        ?array $positionContext,
        string $companyCriteria
    ): string {
        $positionText = $positionContext
            ? json_encode(
                $positionContext,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
            : 'Tidak ada posisi target yang dipilih.';

        $companyText = $companyCriteria !== ''
            ? $companyCriteria
            : 'Tidak ada kriteria perusahaan tambahan yang diberikan.';

        return <<<PROMPT
Analisis CV kandidat berikut untuk kebutuhan screening HR.

TARGET POSITION SELECTED BY HR (AUTHORITATIVE):
{$positionText}

COMPANY / ADDITIONAL REQUIREMENTS:
{$companyText}

SUBMITTED CV:
{$cvText}

Tugas:
1. Ekstrak profil kandidat hanya dari informasi yang tertulis di CV.
2. Jika TARGET POSITION berisi data posisi, posisi tersebut adalah target yang WAJIB digunakan dalam analisis. Jangan menggantinya dengan jabatan saat ini atau jabatan yang tertulis di CV kandidat.
3. Untuk posisi yang dipilih, evaluasi nama posisi, deskripsi posisi, dan setiap item jobdesk yang diberikan. Petakan jobdesk satu per satu ke dalam jobdesk_alignment; jangan menghilangkan item hanya karena CV tidak menyebutkannya.
4. Jika posisi target diberikan, position_alignment.status tidak boleh not_assessed. Nilai dukungan bukti CV dengan strong, moderate, atau limited. Jika definisi posisi tidak memiliki deskripsi/jobdesk yang cukup, jelaskan keterbatasan itu di summary dan jangan mengarang requirement.
5. Jika posisi target benar-benar tidak diberikan, jangan membuat kesimpulan posisi yang cocok dan tandai position alignment sebagai not_assessed.
6. Bila company requirements tersedia, bandingkan satu per satu. Bila tidak tersedia, tandai company alignment sebagai not_assessed.
7. Berikan bukti atau kutipan singkat dari CV untuk setiap requirement yang dinilai.
8. Tandai informasi yang tidak ada sebagai not_found, bukan asumsi bahwa kandidat tidak memilikinya.
9. Tandai klaim yang saling bertentangan atau tidak jelas sebagai conflicting dan masukkan ke verification_items.
10. Susun strengths, gaps, hal yang perlu diverifikasi, serta pertanyaan interview yang relevan.
11. Jangan menggunakan usia, gender, agama, ras/etnis, status keluarga, kesehatan/disabilitas, foto, atau karakteristik sensitif lainnya sebagai kriteria.
12. Jangan memberikan keputusan akhir penerimaan/rejection kandidat.
PROMPT;
    }

    protected function extractCvText(string $path, string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => $this->extractPdfText($path),
            'docx' => $this->extractDocxText($path),
            'txt' => trim((string) file_get_contents($path)),
            default => throw new \RuntimeException('Format CV tidak didukung.'),
        };
    }

    protected function extractPdfText(string $path): string
    {
        try {
            $process = new Process(['pdftotext', '-layout', $path, '-']);
            $process->setTimeout(45);
            $process->run();

            if (! $process->isSuccessful()) {
                throw new \RuntimeException(
                    trim($process->getErrorOutput()) ?: 'pdftotext gagal membaca file PDF.'
                );
            }

            return trim($process->getOutput());
        } catch (Throwable $exception) {
            throw new \RuntimeException(
                'PDF tidak dapat dibaca di server. Pastikan utilitas pdftotext tersedia.',
                0,
                $exception
            );
        }
    }

    protected function extractDocxText(string $path): string
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Dokumen DOCX tidak dapat dibuka.');
        }

        $content = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($content === false) {
            throw new \RuntimeException('Konten utama dokumen DOCX tidak ditemukan.');
        }

        $content = preg_replace('/<w:tab[^>]*\/>/i', "\t", $content);
        $content = preg_replace('/<w:br[^>]*\/>/i', "\n", $content);
        $content = preg_replace('/<\/w:p>/i', "\n", $content);

        return trim(html_entity_decode(
            strip_tags((string) $content),
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        ));
    }
}
