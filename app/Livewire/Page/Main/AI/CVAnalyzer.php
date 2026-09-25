<?php

namespace App\Livewire\Page\Main\AI;

use App\Ai\Agents\CVAnalyzer as CVAnalyzerAgent;
use App\Models\Position;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

#[Layout('layouts.main', ['title' => 'CV Analyzer'])]
class CVAnalyzer extends Component
{
    use WithFileUploads;

    public $cvFile = null;

    public string $positionId = '';

    public string $companyCriteria = '';

    public bool $isAnalyzing = false;

    public ?string $errorMessage = null;

    public ?array $analysisResult = null;

    public function mount(): void
    {
        $this->authorize('view-cv-analyzer');
    }

    public function analyze(): void
    {
        $this->authorize('view-cv-analyzer');

        $this->resetErrorBag();
        $this->errorMessage = null;
        $this->analysisResult = null;

        $this->validate([
            'cvFile' => [
                'required',
                'file',
                'mimes:pdf,docx,txt',
                'max:10240',
            ],
            'positionId' => ['nullable', 'integer', 'exists:positions,id'],
            'companyCriteria' => ['nullable', 'string', 'max:5000'],
        ], [
            'cvFile.required' => 'Silakan upload CV terlebih dahulu.',
            'cvFile.mimes' => 'Format CV yang didukung: PDF, DOCX, atau TXT.',
            'cvFile.max' => 'Ukuran CV maksimal 10 MB.',
        ]);

        $this->isAnalyzing = true;

        try {
            $position = null;

            if ($this->positionId !== '') {
                $position = Position::query()
                    ->with('jobdesk')
                    ->where('is_active', 'active')
                    ->findOrFail((int) $this->positionId);
            }

            $cvText = $this->extractCvText();

            if (mb_strlen(trim($cvText)) < 80) {
                throw new \RuntimeException(
                    'Isi CV tidak cukup untuk dianalisis. Pastikan dokumen memiliki teks yang dapat dibaca.'
                );
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

            $criteria = trim($this->companyCriteria);

            $prompt = $this->buildPrompt(
                cvText: Str::limit($cvText, 60000, ''),
                positionContext: $positionContext,
                companyCriteria: $criteria,
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

            $this->analysisResult = [
                'candidate' => $structured['candidate'],
                'position_alignment' => $structured['position_alignment'],
                'company_alignment' => $structured['company_alignment'],
                'strengths' => $structured['strengths'],
                'gaps' => $structured['gaps'],
                'verification_items' => $structured['verification_items'],
                'interview_questions' => $structured['interview_questions'],
                'feedback' => $structured['feedback'],
            ];
        } catch (Throwable $exception) {
            report($exception);

            $this->errorMessage = 'Analisis CV gagal diproses. Pastikan file dapat dibaca dan konfigurasi AI tersedia.';
        } finally {
            $this->isAnalyzing = false;
        }
    }

    public function resetAnalysis(): void
    {
        $this->reset([
            'cvFile',
            'positionId',
            'companyCriteria',
            'analysisResult',
            'errorMessage',
        ]);

        $this->resetErrorBag();
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

        throw new \RuntimeException(
            'AI mengembalikan output yang bukan JSON valid.'
        );
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

    protected function extractCvText(): string
    {
        $path = $this->cvFile->getRealPath();
        $extension = strtolower($this->cvFile->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->extractPdfText($path),
            'docx' => $this->extractDocxText($path),
            'txt' => trim((string) file_get_contents($path)),
            default => throw new \RuntimeException('Format CV tidak didukung.'),
        };
    }

    protected function extractPdfText(string $path): string
    {
        try {
            $process = new Process([
                'pdftotext',
                '-layout',
                $path,
                '-',
            ]);

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

        $content = preg_replace('/<w:tab[^>]*\/>/i', "	", $content);
        $content = preg_replace('/<w:br[^>]*\/>/i', "
", $content);
        $content = preg_replace('/<\/w:p>/i', "
", $content);

        return trim(html_entity_decode(
            strip_tags((string) $content),
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        ));
    }

    public function render()
    {
        $positions = Position::query()
            ->where('is_active', 'active')
            ->with('jobdesk')
            ->orderBy('name')
            ->get();

        return view('livewire.page.main.ai.cv-analyzer', compact('positions'));
    }
}
