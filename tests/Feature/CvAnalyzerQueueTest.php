<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeCvJob;
use App\Livewire\Page\Main\AI\CVAnalyzer;
use App\Models\CvAnalysisRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CvAnalyzerQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('view-cv-analyzer', 'web');
        $this->actingAs(User::factory()->create());
        auth()->user()->givePermissionTo('view-cv-analyzer');

        config(['queue.default' => 'database']);
    }

    public function test_valid_cv_upload_is_queued_without_running_the_analysis_in_the_http_request(): void
    {
        Bus::fake();

        $txt = str_repeat(
            "Nama: Kandidat Tes\nPengalaman software engineer, Laravel, database, API, pendidikan dan proyek.\n",
            3
        );

        Livewire::test(CVAnalyzer::class)
            ->set('cvFile', UploadedFile::fake()->createWithContent('candidate.txt', $txt))
            ->set('companyCriteria', 'Menguasai API')
            ->call('analyze')
            ->assertSet('isAnalyzing', true)
            ->assertSet('errorMessage', null)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cv_analysis_runs', [
            'status' => 'queued',
            'company_criteria' => 'Menguasai API',
        ]);

        Bus::assertDispatched(AnalyzeCvJob::class);
    }

    public function test_user_can_poll_a_completed_analysis_owned_by_their_account(): void
    {
        $run = CvAnalysisRun::query()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => auth()->id(),
            'status' => 'completed',
            'original_filename' => 'candidate.txt',
            'file_path' => storage_path('app/private/cv-analysis/unused.txt'),
            'file_disk' => 'local',
            'result' => [
                'candidate' => ['name' => 'Kandidat Tes'],
                'position_alignment' => [],
                'company_alignment' => [],
                'strengths' => [],
                'gaps' => [],
                'verification_items' => [],
                'interview_questions' => [],
                'feedback' => 'Perlu verifikasi lanjutan.',
            ],
            'finished_at' => now(),
        ]);

        Livewire::test(CVAnalyzer::class)
            ->set('analysisRunId', $run->id)
            ->call('refreshAnalysisStatus')
            ->assertSet('isAnalyzing', false)
            ->assertSet('analysisResult.candidate.name', 'Kandidat Tes');
    }

    public function test_a_user_cannot_read_another_users_analysis_result(): void
    {
        $otherUser = User::factory()->create();

        $run = CvAnalysisRun::query()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $otherUser->id,
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
            ->assertSet('errorMessage', 'Data proses analisis tidak ditemukan. Silakan mulai kembali.');
    }
}
