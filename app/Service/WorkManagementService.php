<?php

namespace App\Service;

use App\Models\DivisionProject;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\ProjectProgressUpdate;
use App\Models\ProjectReview;
use App\Models\Task;
use App\Models\TaskReview;
use App\Models\Team;
use App\Models\WorkManagementAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkManagementService
{
    public function createMasterProject(
        Employees $creator,
        string $name,
        ?string $description = null,
        $startDate = null,
        $dueDate = null,
    ): MasterProject {
        $this->ensureActiveEmployee($creator);

        return DB::transaction(function () use ($creator, $name, $description, $startDate, $dueDate) {
            $project = MasterProject::create([
                'created_by' => $creator->id,
                'name' => $name,
                'description' => $description,
                'start_date' => $startDate,
                'due_date' => $dueDate,
                'status' => 'draft',
            ]);

            $this->audit($creator, $project, 'master_project.created', null, [
                'name' => $project->name,
                'status' => $project->status,
            ]);

            return $project;
        });
    }

    public function createDivisionProject(
        MasterProject $masterProject,
        Divisi $division,
        Employees $creator,
        string $name,
        ?string $description = null,
        $startDate = null,
        $dueDate = null,
        bool $isRequired = true,
    ): DivisionProject {
        $this->ensureActiveEmployee($creator);

        if ($division->is_active !== 'active') {
            throw ValidationException::withMessages(['divisi_id' => 'Divisi harus aktif.']);
        }

        if (! $division->manager_id) {
            throw ValidationException::withMessages(['divisi_id' => 'Divisi belum memiliki manager.']);
        }

        if (DivisionProject::query()
            ->where('master_project_id', $masterProject->id)
            ->where('divisi_id', $division->id)
            ->exists()) {
            throw ValidationException::withMessages(['divisi_id' => 'Divisi tersebut sudah memiliki division project pada master project ini.']);
        }

        return DB::transaction(function () use ($masterProject, $division, $creator, $name, $description, $startDate, $dueDate, $isRequired) {
            $project = $masterProject->divisionProjects()->create([
                'divisi_id' => $division->id,
                'manager_id' => $division->manager_id,
                'created_by' => $creator->id,
                'name' => $name,
                'description' => $description,
                'start_date' => $startDate,
                'due_date' => $dueDate,
                'is_required' => $isRequired,
                'manual_progress' => 0,
                'status' => 'draft',
            ]);

            if ($masterProject->status === 'draft') {
                $masterProject->update(['status' => 'in_progress']);
            }

            $this->audit($creator, $project, 'division_project.created', null, [
                'divisi_id' => $project->divisi_id,
                'manager_id' => $project->manager_id,
                'is_required' => $project->is_required,
                'status' => $project->status,
            ]);

            return $project;
        });
    }

    public function assignTeam(DivisionProject $divisionProject, Team $team, Employees $actor): DivisionProject
    {
        $this->ensureActiveEmployee($actor);

        if ($divisionProject->status === 'completed') {
            throw ValidationException::withMessages(['division_project' => 'Division project yang sudah selesai tidak dapat menerima team baru.']);
        }

        if ($team->is_active !== 'active') {
            throw ValidationException::withMessages(['team_id' => 'Team harus aktif.']);
        }

        if ((int) $team->divisi_id !== (int) $divisionProject->divisi_id) {
            throw ValidationException::withMessages(['team_id' => 'Team harus berada pada divisi yang sama.']);
        }

        if (! $team->supervisor_id) {
            throw ValidationException::withMessages(['team_id' => 'Team harus memiliki supervisor.']);
        }

        if ($divisionProject->teams()->whereKey($team->id)->exists()) {
            throw ValidationException::withMessages(['team_id' => 'Team sudah ditugaskan pada division project ini.']);
        }

        DB::transaction(function () use ($divisionProject, $team, $actor) {
            $divisionProject->teams()->attach($team->id, ['assigned_by' => $actor->id]);

            if ($divisionProject->status === 'draft') {
                $divisionProject->update(['status' => 'in_progress']);
            }

            $this->audit($actor, $divisionProject, 'division_project.team_assigned', null, [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'assigned_by' => $actor->id,
            ]);
        });

        return $divisionProject->refresh();
    }

    public function createTask(
        DivisionProject $divisionProject,
        Team $team,
        Employees $assignee,
        Employees $creator,
        string $title,
        ?string $description = null,
        $dueDate = null,
    ): Task {
        $this->ensureActiveEmployee($creator);
        $this->ensureActiveEmployee($assignee);
        $this->ensureTaskWorkerAccount($assignee);

        if ($divisionProject->status === 'completed') {
            throw ValidationException::withMessages(['division_project' => 'Division project sudah selesai.']);
        }

        $this->ensureCanManageDivisionProject($divisionProject, $actor);

        if (! $divisionProject->teams()->whereKey($team->id)->exists()) {
            throw ValidationException::withMessages(['team_id' => 'Team belum ditugaskan pada division project.']);
        }

        if ($team->is_active !== 'active' || (int) $team->divisi_id !== (int) $divisionProject->divisi_id) {
            throw ValidationException::withMessages(['team_id' => 'Team tidak valid untuk division project ini.']);
        }

        if ((int) $assignee->team_id !== (int) $team->id) {
            throw ValidationException::withMessages(['assignee_id' => 'Assignee harus merupakan anggota team yang dipilih.']);
        }

        if ((int) $assignee->id === (int) $team->supervisor_id) {
            throw ValidationException::withMessages(['assignee_id' => 'Supervisor team tidak dapat menjadi assignee task biasa.']);
        }

        if ((int) $assignee->id === (int) $divisionProject->manager_id) {
            throw ValidationException::withMessages(['assignee_id' => 'Manager division project tidak dapat menjadi assignee task biasa.']);
        }

        if (! ($creator->user?->hasRole('general-manager') || (int) $team->supervisor_id === (int) $creator->id)) {
            throw ValidationException::withMessages(['creator' => 'Task hanya dapat dibuat oleh General Manager atau supervisor team terkait.']);
        }

        if ((int) $assignee->id === (int) $creator->id && (int) $creator->id === (int) $team->supervisor_id) {
            throw ValidationException::withMessages(['assignee_id' => 'Supervisor tidak dapat menugaskan task kepada dirinya sendiri.']);
        }

        return DB::transaction(function () use ($divisionProject, $team, $assignee, $creator, $title, $description, $dueDate) {
            $task = $divisionProject->tasks()->create([
                'team_id' => $team->id,
                'assignee_id' => $assignee->id,
                'created_by' => $creator->id,
                'title' => $title,
                'description' => $description,
                'progress' => 0,
                'status' => Task::STATUS_TO_DO,
                'due_date' => $dueDate,
            ]);

            if (in_array($divisionProject->status, ['draft', 'revision_required'], true)) {
                $divisionProject->update(['status' => 'in_progress']);
            }

            $this->audit($creator, $task, 'task.created', null, [
                'division_project_id' => $divisionProject->id,
                'team_id' => $team->id,
                'assignee_id' => $assignee->id,
                'status' => $task->status,
            ]);

            return $task;
        });
    }

    public function updateTaskWork(
        Task $task,
        Employees $actor,
        int $progress,
        ?string $result = null,
        ?string $blockedReason = null,
        string $workStatus = Task::STATUS_IN_PROGRESS,
    ): Task {
        $this->ensureTaskWorker($task, $actor);

        if ($workStatus === Task::STATUS_BLOCKED && $task->status !== Task::STATUS_IN_PROGRESS) {
            throw ValidationException::withMessages(['status' => 'Task hanya dapat berubah menjadi blocked dari in_progress.']);
        }

        if (! in_array($task->status, [Task::STATUS_TO_DO, Task::STATUS_IN_PROGRESS, Task::STATUS_BLOCKED], true)) {
            throw ValidationException::withMessages(['task' => 'Task tidak berada pada status yang dapat dikerjakan.']);
        }

        if ($progress < 0 || $progress > 100) {
            throw ValidationException::withMessages(['progress' => 'Progress harus berada di antara 0 sampai 100.']);
        }

        if (! in_array($workStatus, [Task::STATUS_IN_PROGRESS, Task::STATUS_BLOCKED], true)) {
            throw ValidationException::withMessages(['status' => 'Status kerja tidak valid.']);
        }

        if ($workStatus === Task::STATUS_BLOCKED && blank($blockedReason)) {
            throw ValidationException::withMessages(['blockedReason' => 'Alasan blocked wajib diisi.']);
        }

        $old = [
            'progress' => $task->progress,
            'status' => $task->status,
            'result' => $task->result,
            'blocked_reason' => $task->blocked_reason,
        ];

        $task->update([
            'progress' => $progress,
            'status' => $workStatus,
            'result' => $result,
            'blocked_reason' => $workStatus === Task::STATUS_BLOCKED ? $blockedReason : null,
        ]);

        $task = $task->refresh();

        $this->audit($actor, $task, 'task.work_updated', $old, [
            'progress' => $task->progress,
            'status' => $task->status,
            'result' => $task->result,
            'blocked_reason' => $task->blocked_reason,
        ]);

        return $task;
    }

    public function submitTask(Task $task, Employees $actor): Task
    {
        $this->ensureTaskWorker($task, $actor);

        if ($task->status !== Task::STATUS_IN_PROGRESS) {
            throw ValidationException::withMessages(['task' => 'Task hanya dapat disubmit dari status in_progress.']);
        }

        if ((int) $task->progress !== 100) {
            throw ValidationException::withMessages(['progress' => 'Task harus mencapai progress 100% sebelum disubmit.']);
        }

        $old = ['status' => $task->status, 'submitted_at' => $task->submitted_at];

        $task->update([
            'status' => Task::STATUS_IN_REVIEW,
            'submitted_at' => now(),
            'blocked_reason' => null,
        ]);

        $task = $task->refresh();

        $this->audit($actor, $task, 'task.submitted', $old, [
            'status' => $task->status,
            'submitted_at' => $task->submitted_at?->toISOString(),
        ]);

        return $task;
    }

    public function reviewTask(Task $task, Employees $reviewer, string $decision, ?string $feedback = null): TaskReview
    {
        $task->loadMissing('team', 'divisionProject');

        if ((int) $task->team?->supervisor_id !== (int) $reviewer->id) {
            throw ValidationException::withMessages(['reviewer' => 'Task hanya dapat direview oleh supervisor team terkait.']);
        }

        if ($task->status !== Task::STATUS_IN_REVIEW) {
            throw ValidationException::withMessages(['task' => 'Task harus berada pada status in_review.']);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'Decision review tidak valid.']);
        }

        return DB::transaction(function () use ($task, $reviewer, $decision, $feedback) {
            $review = $task->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $old = [
                'status' => $task->status,
                'progress' => $task->progress,
                'completed_at' => $task->completed_at,
            ];

            $task->update([
                'status' => $decision === 'approved' ? Task::STATUS_DONE : Task::STATUS_IN_PROGRESS,
                'progress' => $decision === 'approved' ? 100 : min((int) $task->progress, 99),
                'completed_at' => $decision === 'approved' ? now() : null,
            ]);

            $task = $task->refresh();

            $this->audit($reviewer, $task, 'task.reviewed', $old, [
                'status' => $task->status,
                'progress' => $task->progress,
                'completed_at' => $task->completed_at?->toISOString(),
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $this->syncDivisionProjectStatus($task->divisionProject()->firstOrFail());

            return $review;
        });
    }

    public function cancelTask(Task $task, Employees $actor): Task
    {
        $task->loadMissing('divisionProject', 'team');
        $this->ensureActiveEmployee($actor);
        if (! ($actor->user?->hasRole('general-manager')
            || (int) $task->divisionProject?->manager_id === (int) $actor->id
            || (int) $task->team?->supervisor_id === (int) $actor->id)) {
            throw ValidationException::withMessages(['actor' => 'Actor tidak memiliki scope untuk membatalkan task ini.']);
        }

        if (in_array($task->status, [Task::STATUS_DONE, Task::STATUS_CANCELLED], true)) {
            throw ValidationException::withMessages(['task' => 'Task tidak dapat dibatalkan pada status saat ini.']);
        }

        $old = ['status' => $task->status];
        $task->update(['status' => Task::STATUS_CANCELLED, 'completed_at' => null]);
        $task = $task->refresh();

        $this->audit($actor, $task, 'task.cancelled', $old, ['status' => $task->status]);
        $this->syncDivisionProjectStatus($task->divisionProject()->firstOrFail());

        return $task;
    }

    public function reportManualProgress(DivisionProject $divisionProject, Employees $reporter, int $progress, ?string $note = null): ProjectProgressUpdate
    {
        $divisionProject->loadMissing('teams');
        $isManager = (int) $divisionProject->manager_id === (int) $reporter->id;
        $isSupervisor = $divisionProject->teams->contains(fn ($team) => (int) $team->supervisor_id === (int) $reporter->id);
        if (! $isManager && ! $isSupervisor) {
            throw ValidationException::withMessages(['reporter' => 'Reporter tidak memiliki scope untuk division project ini.']);
        }

        if ($progress < 0 || $progress > 100) {
            throw ValidationException::withMessages(['progress' => 'Progress harus berada di antara 0 sampai 100.']);
        }

        return DB::transaction(function () use ($divisionProject, $reporter, $progress, $note) {
            $old = ['manual_progress' => $divisionProject->manual_progress];

            $update = $divisionProject->progressUpdates()->create([
                'reported_by' => $reporter->id,
                'progress' => $progress,
                'note' => $note,
            ]);

            $divisionProject->update(['manual_progress' => $progress]);

            $this->audit($reporter, $divisionProject, 'division_project.manual_progress_reported', $old, [
                'manual_progress' => $progress,
                'note' => $note,
            ]);

            return $update;
        });
    }

    public function reviewDivisionProject(DivisionProject $divisionProject, Employees $reviewer, string $decision, ?string $feedback = null): ProjectReview
    {
        if ((int) $divisionProject->manager_id !== (int) $reviewer->id) {
            throw ValidationException::withMessages(['reviewer' => 'Hanya manager division project yang dapat melakukan review.']);
        }

        if ($divisionProject->status !== 'ready_for_review') {
            throw ValidationException::withMessages(['division_project' => 'Division project harus siap direview.']);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'Decision review tidak valid.']);
        }

        return DB::transaction(function () use ($divisionProject, $reviewer, $decision, $feedback) {
            $review = $divisionProject->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'reviewer_level' => 'manager',
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $old = ['status' => $divisionProject->status];

            $divisionProject->update(['status' => $decision === 'approved' ? 'completed' : 'revision_required']);
            $divisionProject = $divisionProject->refresh();

            $this->audit($reviewer, $divisionProject, 'division_project.reviewed', $old, [
                'status' => $divisionProject->status,
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $this->syncMasterProjectStatus($divisionProject->masterProject()->firstOrFail());

            return $review;
        });
    }

    public function reviewMasterProject(MasterProject $masterProject, Employees $reviewer, string $decision, ?string $feedback = null): ProjectReview
    {
        if (! $reviewer->user?->hasRole('general-manager')) {
            throw ValidationException::withMessages(['reviewer' => 'Hanya General Manager yang dapat melakukan final approval.']);
        }

        if ($masterProject->status !== 'ready_for_review') {
            throw ValidationException::withMessages(['master_project' => 'Master project belum siap untuk final approval.']);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'Decision review tidak valid.']);
        }

        return DB::transaction(function () use ($masterProject, $reviewer, $decision, $feedback) {
            $review = $masterProject->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'reviewer_level' => 'general_manager',
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $old = [
                'status' => $masterProject->status,
                'approved_by' => $masterProject->approved_by,
                'approved_at' => $masterProject->approved_at,
            ];

            $masterProject->update([
                'status' => $decision === 'approved' ? 'completed' : 'in_progress',
                'approved_by' => $decision === 'approved' ? $reviewer->id : null,
                'approved_at' => $decision === 'approved' ? now() : null,
            ]);

            $masterProject = $masterProject->refresh();

            $this->audit($reviewer, $masterProject, 'master_project.final_reviewed', $old, [
                'status' => $masterProject->status,
                'approved_by' => $masterProject->approved_by,
                'approved_at' => $masterProject->approved_at?->toISOString(),
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            return $review;
        });
    }

    public function syncDivisionProjectStatus(DivisionProject $divisionProject): DivisionProject
    {
        if ($divisionProject->status === 'completed') {
            return $divisionProject;
        }

        $divisionProject->loadMissing('tasks');

        if ($divisionProject->allRequiredWorkCompleted()) {
            $divisionProject->update(['status' => 'ready_for_review']);
        } elseif ($divisionProject->tasks->isNotEmpty()
            && ! in_array($divisionProject->status, ['in_progress', 'revision_required'], true)) {
            $divisionProject->update(['status' => 'in_progress']);
        }

        return $divisionProject->refresh();
    }

    public function syncMasterProjectStatus(MasterProject $masterProject): MasterProject
    {
        if ($masterProject->status === 'completed') {
            return $masterProject;
        }

        $requiredProjects = $masterProject->divisionProjects()->where('is_required', true)->get();

        if ($requiredProjects->isNotEmpty()
            && $requiredProjects->every(fn (DivisionProject $project) => $project->status === 'completed')) {
            $masterProject->update(['status' => 'ready_for_review']);
        } elseif ($masterProject->status === 'ready_for_review') {
            $masterProject->update(['status' => 'in_progress']);
        }

        return $masterProject->refresh();
    }

    private function ensureTaskWorker(Task $task, Employees $actor): void
    {
        if ((int) $task->assignee_id !== (int) $actor->id) {
            throw ValidationException::withMessages(['task' => 'Hanya task worker yang ditugaskan yang dapat mengubah pekerjaan task.']);
        }

        $this->ensureActiveEmployee($actor);
        $this->ensureTaskWorkerAccount($actor);
    }

    private function ensureTaskWorkerAccount(Employees $employee): void
    {
        if (! $employee->user?->hasRole('task-worker') || $employee->user?->status !== 'active') {
            throw ValidationException::withMessages(['employee' => 'Task worker harus memiliki akun aktif dan role task-worker.']);
        }
    }

    private function ensureCanManageDivisionProject(DivisionProject $divisionProject, Employees $actor): void
    {
        if (! ($actor->user?->hasRole('general-manager') || (int) $divisionProject->manager_id === (int) $actor->id)) {
            throw ValidationException::withMessages(['actor' => 'Actor tidak memiliki scope untuk division project ini.']);
        }
    }

    private function ensureActiveEmployee(Employees $employee): void
    {
        if ($employee->status_employee !== 'active') {
            throw ValidationException::withMessages(['employee' => 'Karyawan harus aktif.']);
        }
    }

    private function audit(Employees $actor, object $auditable, string $action, ?array $oldValues, ?array $newValues, ?string $note = null): void
    {
        WorkManagementAudit::create([
            'actor_id' => $actor->id,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'note' => $note,
        ]);
    }
}
