<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeCvJob;
use App\Livewire\Page\Main\AI\CVAnalyzer;
use App\Models\CvAnalysisRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CvAnalyzerDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('view-cv-analyzer', 'web');
        $user = User::factory()->create();
        $user->givePermissionTo('view-cv-analyzer');
        $this->actingAs($user);

        config(['queue.default' => 'database']);
        Storage::fake('local');
    }

    public function test_upload_is_persisted_and_analysis_job_is_dispatched(): void
    {
        Bus::fake();

        $cvText = str_repeat(
            "Pengalaman software engineer dengan Laravel, REST API, database, dan pengembangan aplikasi.\n",
            3
        );

        Livewire::test(CVAnalyzer::class)
            ->set('cvFile', UploadedFile::fake()->createWithContent('candidate.txt', $cvText))
            ->set('companyCriteria', 'Menguasai REST API')
            ->call('analyze')
            ->assertHasNoErrors()
            ->assertSet('isAnalyzing', true)
            ->assertSet('errorMessage', null);

        $run = CvAnalysisRun::query()->firstOrFail();

        $this->assertSame('queued', $run->status);
        $this->assertSame('Menguasai REST API', $run->company_criteria);
        $this->assertSame(auth()->id(), $run->user_id);

        Storage::disk('local')->assertExists('cv-analysis/'.basename($run->file_path));
        Bus::assertDispatched(AnalyzeCvJob::class, fn (AnalyzeCvJob $job) => true);
    }

    public function test_status_poll_only_returns_a_run_owned_by_the_current_user(): void
    {
        $anotherUser = User::factory()->create();

        $run = CvAnalysisRun::query()->create([
            'id' => (string) Str::uuid(),
            'user_id' => $anotherUser->id,
            'status' => 'completed',
            'original_filename' => 'candidate.txt',
            'file_path' => storage_path('app/private/cv-analysis/unused.txt'),
            'file_disk' => 'local',
            'result' => ['candidate' => ['name' => 'Private Candidate']],
            'finished_at' => now(),
        ]);

        Livewire::test(CVAnalyzer::class)
            ->set('analysisRunId', $run->id)
            ->call('refreshAnalysisStatus')
            ->assertSet('analysisResult', null)
            ->assertSet('analysisRunId', null)
            ->assertSet('errorMessage', 'Data proses analisis tidak ditemukan. Silakan mulai kembali.');
    }
}
