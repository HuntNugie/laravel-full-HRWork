<?php

namespace App\Services\WorkManagement;

use App\Models\DivisionProject;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\ProjectProgressUpdate;
use App\Models\ProjectReview;
use App\Models\Task;
use App\Models\TaskReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkManagementService
{
    public function assignTask(
        DivisionProject $divisionProject,
        Employees $assignee,
        Employees $creator,
        string $title,
        ?string $description = null,
        $dueDate = null,
    ): Task {
        $divisionProject->loadMissing('division', 'teams');
        $this->ensureAssigneeCanBeAssigned($divisionProject, $assignee, $creator);

        $team = $assignee->team;

        if (! $team || ! $divisionProject->teams()->whereKey($team->getKey())->exists()) {
            throw ValidationException::withMessages([
                'assignee_id' => 'Karyawan harus berasal dari team yang ditugaskan pada division project.',
            ]);
        }

        return $divisionProject->tasks()->create([
            'team_id' => $team->getKey(),
            'assignee_id' => $assignee->getKey(),
            'created_by' => $creator->getKey(),
            'title' => $title,
            'description' => $description,
            'due_date' => $dueDate,
            'status' => 'todo',
        ]);
    }

    public function submitTask(Task $task, Employees $actor): Task
    {
        if ((int) $task->assignee_id !== (int) $actor->getKey()) {
            throw ValidationException::withMessages(['task' => 'Hanya assignee task yang dapat melakukan submit.']);
        }

        if (! in_array($task->status, ['todo', 'in_progress'], true)) {
            throw ValidationException::withMessages(['task' => 'Task tidak berada pada status yang dapat disubmit.']);
        }

        $task->update(['status' => 'submitted', 'submitted_at' => now()]);

        return $task->refresh();
    }

    public function reviewTask(Task $task, Employees $reviewer, string $decision, ?string $feedback = null): TaskReview
    {
        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'Decision review tidak valid.']);
        }

        $task->loadMissing('team');

        if (! $task->team || (int) $task->team->supervisor_id !== (int) $reviewer->getKey()) {
            throw ValidationException::withMessages(['reviewer' => 'Task hanya dapat direview oleh supervisor team terkait.']);
        }

        if ($task->status !== 'submitted') {
            throw ValidationException::withMessages(['task' => 'Task harus berstatus submitted sebelum direview.']);
        }

        return DB::transaction(function () use ($task, $reviewer, $decision, $feedback) {
            $review = $task->reviews()->create([
                'reviewer_id' => $reviewer->getKey(),
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $task->update([
                'status' => $decision === 'approved' ? 'approved' : 'in_progress',
                'approved_at' => $decision === 'approved' ? now() : null,
            ]);

            return $review;
        });
    }

    public function reportProgress(
        DivisionProject $divisionProject,
        Employees $reporter,
        int $progress,
        ?string $note = null,
    ): ProjectProgressUpdate {
        if ($progress < 0 || $progress > 100) {
            throw ValidationException::withMessages(['progress' => 'Progress harus berada di antara 0 sampai 100.']);
        }

        $divisionProject->loadMissing('division', 'teams');

        $isManager = (int) optional($divisionProject->division)->manager_id === (int) $reporter->getKey();
        $isAssignedSupervisor = $divisionProject->teams->contains(fn ($team) => (int) $team->supervisor_id === (int) $reporter->getKey());

        if (! $isManager && ! $isAssignedSupervisor) {
            throw ValidationException::withMessages(['reporter' => 'Progress hanya dapat dilaporkan oleh manager divisi atau supervisor team terkait.']);
        }

        return DB::transaction(function () use ($divisionProject, $reporter, $progress, $note) {
            $update = $divisionProject->progressUpdates()->create([
                'reported_by' => $reporter->getKey(),
                'progress' => $progress,
                'note' => $note,
            ]);

            $divisionProject->update([
                'reported_progress' => $progress,
                'status' => $progress === 100 ? 'ready_for_review' : ($progress > 0 ? 'in_progress' : 'draft'),
            ]);

            return $update;
        });
    }

    public function reviewDivisionProject(
        DivisionProject $divisionProject,
        Employees $reviewer,
        string $reviewerLevel,
        string $decision,
        ?string $feedback = null,
    ): ProjectReview {
        if (! in_array($reviewerLevel, ['manager', 'general_manager'], true)) {
            throw ValidationException::withMessages(['reviewer_level' => 'Level reviewer tidak valid.']);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'Decision review tidak valid.']);
        }

        $divisionProject->loadMissing('division');
        $isDivisionManager = (int) optional($divisionProject->division)->manager_id === (int) $reviewer->getKey();
        $hasGeneralManagerRole = $reviewer->user?->hasRole(['general-manager', 'general manager', 'general_manager']) ?? false;

        if (($reviewerLevel === 'manager' && ! $isDivisionManager) || ($reviewerLevel === 'general_manager' && ! $hasGeneralManagerRole)) {
            throw ValidationException::withMessages(['reviewer' => 'Reviewer tidak memiliki kewenangan untuk level review ini.']);
        }

        if ($divisionProject->status !== 'ready_for_review') {
            throw ValidationException::withMessages(['division_project' => 'Division project harus siap direview.']);
        }

        return DB::transaction(function () use ($divisionProject, $reviewer, $reviewerLevel, $decision, $feedback) {
            $review = $divisionProject->reviews()->create([
                'reviewer_id' => $reviewer->getKey(),
                'reviewer_level' => $reviewerLevel,
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            if ($decision === 'rejected') {
                $divisionProject->update(['status' => 'revision_required']);
            } elseif ($reviewerLevel === 'general_manager') {
                $divisionProject->update(['status' => 'completed']);
                $this->syncMasterProjectStatus($divisionProject->masterProject()->firstOrFail());
            }

            return $review;
        });
    }

    private function ensureAssigneeCanBeAssigned(DivisionProject $divisionProject, Employees $assignee, Employees $creator): void
    {
        if ($assignee->status_employee !== 'active') {
            throw ValidationException::withMessages(['assignee_id' => 'Hanya karyawan aktif yang dapat ditugaskan.']);
        }

        if (! $assignee->team_id) {
            throw ValidationException::withMessages(['assignee_id' => 'Karyawan harus memiliki team sebelum ditugaskan.']);
        }

        if ((int) $assignee->getKey() === (int) $creator->getKey() && $creator->supervisorTeam()->exists()) {
            throw ValidationException::withMessages(['assignee_id' => 'Supervisor tidak dapat menugaskan task kepada dirinya sendiri.']);
        }

        if ((int) $assignee->getKey() === (int) optional($divisionProject->division)->manager_id) {
            throw ValidationException::withMessages(['assignee_id' => 'Manager divisi tidak dapat dipilih sebagai assignee task biasa.']);
        }
    }

    private function syncMasterProjectStatus(MasterProject $masterProject): void
    {
        $masterProject->load('divisionProjects');

        if ($masterProject->divisionProjects->isNotEmpty() && $masterProject->divisionProjects->every(fn ($project) => $project->status === 'completed')) {
            $masterProject->update(['status' => 'completed']);
        }
    }
}
