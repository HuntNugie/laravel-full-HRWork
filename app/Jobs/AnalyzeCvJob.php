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
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class AnalyzeCvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 240;

    public int $tries = 1;

    public function __construct(public string $analysisRunId)
    {
        $this->onQueue('cv-analysis');
    }

    public function handle(): void
    {
        $run = CvAnalysisRun::query()->findOrFail($this->analysisRunId);

        if (in_array($run->status, ['completed', 'failed', 'cancelled'], true)) {
            return;
        }

        if (! file_exists($run->file_path)) {
            $run->forceFill([
                'status' => 'failed',
                'error_message' => 'File CV tidak ditemukan saat job dijalankan.',
                'finished_at' => now(),
            ])->save();

            return;
        }

        $run->forceFill([
            'status' => 'processing',
            'started_at' => now(),
            'error_message' => null,
        ])->save();

        try {
            $path = $run->file_path;

            $cvText = $this->extractCvText($path, pathinfo($run->original_filename, PATHINFO_EXTENSION));

            if (mb_strlen(trim($cvText)) < 80) {
                throw new \RuntimeException(
                    'Isi CV tidak cukup untuk dianalisis. Pastikan dokumen memiliki teks yang dapat dibaca.'
                );
            }

            $position = null;

            if ($run->position_id) {
                $position = Position::query()
                    ->with('jobdesk')
                    ->where('is_active', 'active')
                    ->find($run->position_id);

                if (! $position) {
                    throw new \RuntimeException('Posisi target tidak ditemukan atau sudah tidak aktif.');
                }
            }

            $positionContext = $position ? [
                'name' => $position->name,
                'description' => $position->description,
                'jobdesk' => $position->jobdesk
                    ->pluck('jobdesk')
                    ->filter()
                    ->values()
                    ->all(),
            ] : null;

            $prompt = $this->buildPrompt(
                cvText: Str::limit($cvText, 60000, ''),
                positionContext: $positionContext,
                companyCriteria: trim((string) $run->company_criteria),
            );

            $response = CVAnalyzerAgent::make()->prompt(
                $prompt,
                provider: '9router',
                model: config('ai.providers.9router.models.text.default'),
            );

            $structured = $this->decodeAnalysisResponse($response->text);

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

            $run->forceFill([
                'status' => 'completed',
                'result' => $result,
                'error_message' => null,
                'finished_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            report($exception);

            $run->forceFill([
                'status' => 'failed',
                'error_message' => $this->safeErrorMessage($exception),
                'finished_at' => now(),
            ])->save();
        } finally {
            // Do not leave the private source CV on disk after a completed or
            // handled failure. Unexpected process termination leaves it for diagnosis.
            $run->refresh();

            if (in_array($run->status, ['completed', 'failed', 'cancelled'], true)) {
                try {
                    Storage::disk($run->file_disk)->delete(
                        'cv-analysis/'.basename($run->file_path)
                    );
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }
        }
    }

    public function failed(?Throwable $exception): void
    {
        $run = CvAnalysisRun::query()->find($this->analysisRunId);

        if (! $run || in_array($run->status, ['completed', 'failed', 'cancelled'], true)) {
            return;
        }

        if ($exception) {
            report($exception);
        }

        $run->forceFill([
            'status' => 'failed',
            'error_message' => 'Analisis CV gagal dijalankan oleh worker. Silakan coba kembali.',
            'finished_at' => now(),
        ])->save();
    }

    protected function safeErrorMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();

        if (str_contains(strtolower($message), 'timeout') || str_contains(strtolower($message), 'timed out')) {
            return 'Analisis AI melewati batas waktu provider. Silakan coba lagi atau gunakan model yang lebih cepat.';
        }

        if (str_contains($message, 'pdftotext') || str_contains($message, 'PDF')) {
            return 'PDF tidak dapat dibaca di server. Pastikan file PDF berisi teks dan utilitas pdftotext tersedia.';
        }

        if (str_contains($message, 'DOCX')) {
            return 'Dokumen DOCX tidak dapat dibaca. Pastikan file tidak rusak.';
        }

        if (str_contains($message, 'JSON') || str_contains($message, 'AI tidak mengembalikan')) {
            return 'AI memberikan format hasil yang tidak sesuai. Silakan ulangi analisis.';
        }

        return 'Analisis CV gagal diproses. Periksa log aplikasi atau coba kembali.';
    }

    protected function decodeAnalysisResponse(string $text): array
    {
        $text = trim($text);

        if ($text === '') {
            throw new \RuntimeException('AI mengembalikan response kosong.');
        }

        if (preg_match('/\\x60\\x60\\x60(?:json)?\\s*(.*?)\\s*\\x60\\x60\\x60/si', $text, $matches) === 1) {
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

TARGET POSITION:
{$positionText}

COMPANY / ADDITIONAL REQUIREMENTS:
{$companyText}

SUBMITTED CV:
{$cvText}

Tugas:
1. Ekstrak profil kandidat hanya dari informasi yang tertulis di CV.
2. Bila target position tersedia, bandingkan CV dengan requirement dan jobdesk posisi satu per satu.
3. Bila target position tidak tersedia, jangan membuat kesimpulan posisi yang cocok; buat candidate profile dan tandai position alignment sebagai not_assessed.
4. Bila company requirements tersedia, bandingkan satu per satu. Bila tidak tersedia, tandai company alignment sebagai not_assessed.
5. Berikan bukti atau kutipan singkat dari CV untuk setiap requirement yang dinilai.
6. Tandai informasi yang tidak ada sebagai not_found, bukan asumsi bahwa kandidat tidak memilikinya.
7. Tandai klaim yang saling bertentangan atau tidak jelas sebagai conflicting dan masukkan ke verification_items.
8. Susun strengths, gaps, hal yang perlu diverifikasi, serta pertanyaan interview yang relevan.
9. Jangan menggunakan usia, gender, agama, ras/etnis, status keluarga, kesehatan/disabilitas, foto, atau karakteristik sensitif lainnya sebagai kriteria.
10. Jangan memberikan keputusan akhir penerimaan/rejection kandidat.
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
