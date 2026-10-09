<?php

namespace App\Livewire\Page\Main\AI;

use App\Jobs\AnalyzeCvJob;
use App\Models\CvAnalysisRun;
use App\Models\Position;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

#[Layout('layouts.main', ['title' => 'CV Analyzer'])]
class CVAnalyzer extends Component
{
    use WithFileUploads;

    public $cvFile = null;

    public string $positionId = '';

    public string $companyCriteria = '';

    public ?string $analysisRunId = null;

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
            'cvFile' => ['required', 'file', 'mimes:pdf,docx,txt', 'max:10240'],
            'positionId' => ['nullable', 'integer', 'exists:positions,id'],
            'companyCriteria' => ['nullable', 'string', 'max:5000'],
        ], [
            'cvFile.required' => 'Silakan upload CV terlebih dahulu.',
            'cvFile.mimes' => 'Format CV yang didukung: PDF, DOCX, atau TXT.',
            'cvFile.max' => 'Ukuran CV maksimal 10 MB.',
        ]);

        try {
            $positionId = null;

            if ($this->positionId !== '') {
                $positionId = Position::query()
                    ->where('is_active', 'active')
                    ->findOrFail((int) $this->positionId)
                    ->getKey();
            }

            $filename = Str::uuid().'.'.strtolower($this->cvFile->getClientOriginalExtension());
            $storedPath = $this->cvFile->storeAs('cv-analysis', $filename, 'local');

            if (! $storedPath) {
                throw new \RuntimeException('File CV gagal disimpan untuk diproses.');
            }

            try {
                $run = CvAnalysisRun::query()->create([
                    'id' => (string) Str::uuid(),
                    'user_id' => auth()->id(),
                    'status' => 'queued',
                    'original_filename' => $this->cvFile->getClientOriginalName(),
                    'file_path' => storage_path('app/private/'.$storedPath),
                    'file_disk' => 'local',
                    'position_id' => $positionId,
                    'company_criteria' => trim($this->companyCriteria),
                ]);

                $this->analysisRunId = $run->id;
                $this->isAnalyzing = true;

                AnalyzeCvJob::dispatch($run->id)->onConnection('database');
            } catch (Throwable $exception) {
                @unlink(storage_path('app/private/'.$storedPath));

                throw $exception;
            }

            $this->reset('cvFile');
        } catch (Throwable $exception) {
            report($exception);

            $this->analysisRunId = null;
            $this->isAnalyzing = false;
            $this->errorMessage = 'Permintaan analisis belum dapat dimulai. Periksa konfigurasi queue dan penyimpanan file.';
        }
    }

    public function refreshAnalysisStatus(): void
    {
        $this->authorize('view-cv-analyzer');

        if (! $this->analysisRunId) {
            $this->isAnalyzing = false;

            return;
        }

        $run = CvAnalysisRun::query()
            ->whereKey($this->analysisRunId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $run) {
            $this->isAnalyzing = false;
            $this->errorMessage = 'Data proses analisis tidak ditemukan. Silakan mulai kembali.';

            return;
        }

        if (in_array($run->status, ['queued', 'processing'], true)) {
            $this->isAnalyzing = true;

            return;
        }

        $this->isAnalyzing = false;

        if ($run->status === 'completed') {
            $this->analysisResult = $run->result;

            return;
        }

        $this->errorMessage = $run->error_message
            ?: 'Analisis CV gagal diproses. Silakan coba kembali.';
    }

    public function resetAnalysis(): void
    {
        if ($this->analysisRunId) {
            CvAnalysisRun::query()
                ->whereKey($this->analysisRunId)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['queued', 'processing'])
                ->update([
                    'status' => 'cancelled',
                    'error_message' => 'Analisis dibatalkan oleh pengguna.',
                    'finished_at' => now(),
                ]);
        }

        $this->reset([
            'cvFile',
            'positionId',
            'companyCriteria',
            'analysisRunId',
            'isAnalyzing',
            'analysisResult',
            'errorMessage',
        ]);

        $this->resetErrorBag();
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
