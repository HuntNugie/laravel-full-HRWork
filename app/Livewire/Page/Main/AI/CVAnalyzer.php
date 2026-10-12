<?php

namespace App\Livewire\Page\Main\AI;

use App\Jobs\AnalyzeCvJob;
use App\Models\CvAnalysisRun;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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

    public bool $isAnalyzing = false;

    public ?string $analysisRunId = null;

    public ?string $selectedPositionName = null;

    public ?string $errorMessage = null;

    public ?array $analysisResult = null;

    public function mount(): void
    {
        $this->authorize('view-cv-analyzer');
    }

    public function analyze(): void
    {
        $this->authorize('view-cv-analyzer');

        if ($this->isAnalyzing) {
            return;
        }

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

        // Capture the selected position and its jobdesk before dispatching the
        // background job. The analysis should use the exact selection made by HR.
        $positionContext = null;

        if ($this->positionId !== '') {
            $position = Position::query()
                ->with('jobdesk')
                ->where('is_active', 'active')
                ->find((int) $this->positionId);

            if (! $position) {
                throw ValidationException::withMessages([
                    'positionId' => 'Posisi yang dipilih tidak tersedia atau sudah tidak aktif. Silakan pilih ulang.',
                ]);
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
        }

        $run = null;
        $storedPath = null;

        try {
            $extension = strtolower($this->cvFile->getClientOriginalExtension());
            $originalFilename = $this->cvFile->getClientOriginalName();
            $filename = (string) Str::uuid().'.'.$extension;

            // Store the source CV on the private local disk before dispatching.
            // The temporary Livewire upload may be removed after this request.
            $storedPath = $this->cvFile->storeAs('cv-analysis', $filename, 'local');

            if (! $storedPath) {
                throw new \RuntimeException('File CV tidak dapat disimpan untuk diproses.');
            }

            $run = CvAnalysisRun::query()->create([
                'user_id' => auth()->id(),
                'status' => 'queued',
                'original_filename' => $originalFilename,
                'file_path' => $storedPath,
                'file_disk' => 'local',
                'position_id' => $this->positionId !== '' ? (int) $this->positionId : null,
                'position_context' => $positionContext,
                'company_criteria' => trim($this->companyCriteria) !== ''
                    ? trim($this->companyCriteria)
                    : null,
            ]);

            // Use the database connection explicitly so this work never runs
            // inside the HTTP request, even if another queue driver is default.
            AnalyzeCvJob::dispatch($run->id)
                ->onConnection('database')
                ->onQueue('cv-analysis');

            $this->analysisRunId = $run->id;
            $this->isAnalyzing = true;
            $this->cvFile = null;
            $this->positionId = '';
            $this->companyCriteria = '';
        } catch (Throwable $exception) {
            report($exception);

            if ($run) {
                $run->forceFill([
                    'status' => 'failed',
                    'error_message' => 'CV gagal dimasukkan ke antrean analisis.',
                    'finished_at' => now(),
                ])->save();
            }

            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }

            $this->analysisRunId = null;
            $this->isAnalyzing = false;
            $this->errorMessage = 'CV gagal dimasukkan ke antrean analisis. Periksa konfigurasi queue dan coba lagi.';
        }
    }

    /**
     * Refresh only the current user's analysis status.
     *
     * @return bool True while the job is queued or processing.
     */
    public function refreshAnalysisStatus(): bool
    {
        if (! $this->analysisRunId) {
            $this->isAnalyzing = false;

            return false;
        }

        $run = CvAnalysisRun::query()
            ->whereKey($this->analysisRunId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $run) {
            $this->isAnalyzing = false;
            $this->errorMessage = 'Data analisis tidak ditemukan. Silakan ulangi proses.';

            return false;
        }

        if ($run->status === 'completed') {
            if ($run->result === null) {
                $this->isAnalyzing = false;
                $this->errorMessage = 'Analisis selesai, tetapi hasil tidak tersedia. Silakan coba kembali.';

                return false;
            }

            $this->analysisResult = $run->result;
            $this->selectedPositionName = $run->position_context['name'] ?? null;

            if (! $this->selectedPositionName && $run->position_id) {
                $this->selectedPositionName = Position::query()
                    ->whereKey($run->position_id)
                    ->value('name');
            }

            $this->errorMessage = null;
            $this->isAnalyzing = false;

            // This is a one-time analysis, not a history feature. Keep the response
            // in this component's state and remove its database row after delivery.
            $run->delete();
            $this->analysisRunId = null;

            return false;
        }

        if (in_array($run->status, ['failed', 'cancelled'], true)) {
            $this->analysisResult = null;
            $this->errorMessage = $run->error_message
                ?? ($run->status === 'cancelled'
                    ? 'Analisis CV dibatalkan.'
                    : 'Analisis CV gagal diproses. Silakan coba lagi.');
            $this->isAnalyzing = false;

            // Do not retain a failed/cancelled analysis as history either.
            $run->delete();
            $this->analysisRunId = null;

            return false;
        }

        $this->isAnalyzing = true;

        return true;
    }

    public function resetAnalysis(): void
    {
        if ($this->analysisRunId) {
            $run = CvAnalysisRun::query()
                ->whereKey($this->analysisRunId)
                ->where('user_id', auth()->id())
                ->first();

            if ($run && in_array($run->status, ['queued', 'processing'], true)) {
                $run->forceFill([
                    'status' => 'cancelled',
                    'error_message' => 'Analisis dibatalkan oleh pengguna.',
                    'finished_at' => now(),
                ])->save();

                Storage::disk($run->file_disk)->delete($run->file_path);
            }
        }

        $this->reset([
            'cvFile',
            'positionId',
            'companyCriteria',
            'analysisRunId',
            'selectedPositionName',
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
