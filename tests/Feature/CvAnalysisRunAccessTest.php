<?php

namespace Tests\Feature;

use App\Models\CvAnalysisRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CvAnalysisRunAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_analysis_run_can_only_be_loaded_by_its_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $run = CvAnalysisRun::query()->create([
            'id' => (string) Str::uuid(),
            'user_id' => $owner->id,
            'status' => 'completed',
            'original_filename' => 'candidate.txt',
            'file_path' => storage_path('app/private/cv-analysis/unused.txt'),
            'file_disk' => 'local',
            'result' => ['candidate' => ['name' => 'Private Candidate']],
            'finished_at' => now(),
        ]);

        $this->assertTrue(
            CvAnalysisRun::query()
                ->whereKey($run->id)
                ->where('user_id', $owner->id)
                ->exists()
        );

        $this->assertFalse(
            CvAnalysisRun::query()
                ->whereKey($run->id)
                ->where('user_id', $otherUser->id)
                ->exists()
        );
    }
}
