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
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CvAnalyzerQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_cv_analysis_is_queued_and_source_file_is_stored_on_private_disk(): void
    {
        Bus::fake();
        Storage::fake('local');

        $user = $this->actingUserWithCvPermission();

        $component = Livewire::test(CVAnalyzer::class)
            ->set('cvFile', UploadedFile::fake()->createWithContent(
                'candidate.txt',
                str_repeat('Experience in software development, testing, and teamwork. ', 4)
            ))
            ->call('analyze');

        $run = CvAnalysisRun::query()->where('user_id', $user->id)->firstOrFail();

        $component
            ->assertSet('isAnalyzing', true)
            ->assertSet('analysisRunId', $run->id)
            ->assertSet('cvFile', null);

        $this->assertSame('queued', $run->status);
        $this->assertSame('local', $run->file_disk);
        Storage::disk('local')->assertExists($run->file_path);

        Bus::assertDispatched(AnalyzeCvJob::class, function (AnalyzeCvJob $job) use ($run): bool {
            return $job->analysisRunId === $run->id
                && $job->connection === 'database'
                && $job->queue === 'cv-analysis';
        });
    }

    public function test_user_cannot_read_another_users_cv_analysis_result(): void
    {
        $currentUser = $this->actingUserWithCvPermission();
        $otherUser = User::factory()->create();

        $run = CvAnalysisRun::query()->create([
            'user_id' => $otherUser->id,
            'status' => 'completed',
            'original_filename' => 'private-cv.txt',
            'file_path' => 'cv-analysis/private-cv.txt',
            'file_disk' => 'local',
            'result' => ['candidate' => ['name' => 'Private Candidate']],
            'finished_at' => now(),
        ]);

        Livewire::test(CVAnalyzer::class)
            ->set('analysisRunId', $run->id)
            ->call('refreshAnalysisStatus')
            ->assertSet('analysisResult', null)
            ->assertSet('isAnalyzing', false)
            ->assertSet('errorMessage', 'Data analisis tidak ditemukan. Silakan ulangi proses.');

        $this->assertNotSame($currentUser->id, $otherUser->id);
    }

    private function actingUserWithCvPermission(): User
    {
        $permission = Permission::query()->firstOrCreate([
            'name' => 'view-cv-analyzer',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        $this->actingAs($user);

        return $user;
    }
}
