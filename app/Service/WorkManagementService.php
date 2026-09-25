<?php

namespace App\Service;

use App\Models\DivisionProject;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\Task;
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

        if (! $creator->user?->hasRole('general-manager')) {
            throw ValidationException::withMessages([
                'creator' => 'Hanya General Manager yang dapat membuat Master Project.',
            ]);
        }

        return DB::transaction(function () use ($creator, $name, $description, $startDate, $dueDate) {
            $project = MasterProject::create([
                'created_by' => $creator->id,
                'name' => trim($name),
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
        ?Team $initialTeam = null,
    ): DivisionProject {
        $this->ensureActiveEmployee($creator);

        if (! $creator->user?->hasRole('general-manager')) {
            throw ValidationException::withMessages([
                'creator' => 'Hanya General Manager yang dapat membuat Division Project.',
            ]);
        }

        if ($division->is_active !== 'active') {
            throw ValidationException::withMessages([
                'divisi_id' => 'Divisi harus aktif.',
            ]);
        }

        if (! $division->manager_id) {
            throw ValidationException::withMessages([
                'divisi_id' => 'Divisi belum memiliki Manager.',
            ]);
        }

        if (DivisionProject::query()
            ->where('master_project_id', $masterProject->id)
            ->where('divisi_id', $division->id)
            ->exists()) {
            throw ValidationException::withMessages([
                'divisi_id' => 'Divisi tersebut sudah memiliki Division Project pada Master Project ini.',
            ]);
        }

        if ($initialTeam) {
            $this->validateTeamForDivisionProject(null, $initialTeam, $division);
        }

        return DB::transaction(function () use (
            $masterProject,
            $division,
            $creator,
            $name,
            $description,
            $startDate,
            $dueDate,
            $isRequired,
            $initialTeam,
        ) {
            $project = $masterProject->divisionProjects()->create([
                'divisi_id' => $division->id,
                'manager_id' => $division->manager_id,
                'created_by' => $creator->id,
                'name' => trim($name),
                'description' => $description,
                'start_date' => $startDate,
                'due_date' => $dueDate,
                'is_required' => $isRequired,
                'manual_progress' => 0,
                'status' => 'draft',
            ]);

            if ($initialTeam) {
                $project->teams()->attach($initialTeam->id, [
                    'assigned_by' => $creator->id,
                ]);
                $project->update(['status' => 'in_progress']);
            }

            if ($masterProject->status === 'draft') {
                $masterProject->update(['status' => 'in_progress']);
            }

            $this->audit($creator, $project, 'division_project.created', null, [
                'divisi_id' => $project->divisi_id,
                'manager_id' => $project->manager_id,
                'team_id' => $initialTeam?->id,
                'status' => $project->status,
            ]);

            return $project->refresh();
        });
    }

    public function assignTeam(
        DivisionProject $divisionProject,
        Team $team,
        Employees $actor,
    ): DivisionProject {
        $this->ensureActiveEmployee($actor);

        if (! ($actor->user?->hasRole('general-manager')
            || (int) $divisionProject->manager_id === (int) $actor->id)) {
            throw ValidationException::withMessages([
                'actor' => 'Hanya General Manager atau Manager Division Project yang dapat memilih Team.',
            ]);
        }

        if (! in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true)) {
            throw ValidationException::withMessages([
                'division_project' => 'Team tidak dapat diubah setelah Division Project diajukan atau selesai.',
            ]);
        }

        $this->validateTeamForDivisionProject($divisionProject, $team);

        if ($divisionProject->teams()->whereKey($team->id)->exists()) {
            throw ValidationException::withMessages([
                'team_id' => 'Team sudah dipilih pada Division Project ini.',
            ]);
        }

        return DB::transaction(function () use ($divisionProject, $team, $actor) {
            $divisionProject->teams()->attach($team->id, [
                'assigned_by' => $actor->id,
            ]);

            if ($divisionProject->status === 'draft') {
                $divisionProject->update(['status' => 'in_progress']);
            }

            $this->audit($actor, $divisionProject, 'division_project.team_assigned', null, [
                'team_id' => $team->id,
                'team_name' => $team->name,
            ]);

            return $divisionProject->refresh();
        });
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

        if (! $creator->user?->hasRole('supervisor')) {
            throw ValidationException::withMessages([
                'creator' => 'Task hanya dapat dibuat oleh Supervisor.',
            ]);
        }

        if ((int) $team->supervisor_id !== (int) $creator->id) {
            throw ValidationException::withMessages([
                'creator' => 'Supervisor hanya dapat membuat task untuk Team-nya sendiri.',
            ]);
        }

        if (! in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true)) {
            throw ValidationException::withMessages([
                'division_project' => 'Task hanya dapat dibuat pada Division Project yang sedang berjalan atau direvisi.',
            ]);
        }

        $this->validateTeamForDivisionProject($divisionProject, $team);

        if (! $divisionProject->teams()->whereKey($team->id)->exists()) {
            throw ValidationException::withMessages([
                'team_id' => 'Team belum dipilih pada Division Project ini.',
            ]);
        }

        if ((int) $assignee->team_id !== (int) $team->id) {
            throw ValidationException::withMessages([
                'assignee_id' => 'Employee harus merupakan anggota Team yang dipilih.',
            ]);
        }

        if ((int) $assignee->id === (int) $team->supervisor_id) {
            throw ValidationException::withMessages([
                'assignee_id' => 'Supervisor Team tidak menjadi Task Worker untuk task biasa.',
            ]);
        }

        return DB::transaction(function () use (
            $divisionProject,
            $team,
            $assignee,
            $creator,
            $title,
            $description,
            $dueDate,
        ) {
            $task = $divisionProject->tasks()->create([
                'team_id' => $team->id,
                'assignee_id' => $assignee->id,
                'created_by' => $creator->id,
                'title' => trim($title),
                'description' => $description,
                'progress' => 0,
                'status' => Task::STATUS_TO_DO,
                'due_date' => $dueDate,
            ]);

            $assignee->user?->assignRole('employee');
            $assignee->user?->assignRole('task-worker');

            if ($divisionProject->status === 'draft') {
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

    public function toggleTaskCompletion(
        Task $task,
        Employees $actor,
        bool $completed,
    ): Task {
        $this->ensureActiveEmployee($actor);
        $this->ensureTaskWorker($task, $actor);

        $task->loadMissing('divisionProject');

        if ($task->status === Task::STATUS_CANCELLED) {
            throw ValidationException::withMessages([
                'task' => 'Task yang dibatalkan tidak dapat diubah.',
            ]);
        }

        if (in_array($task->divisionProject?->status, ['submitted_to_gm', 'completed'], true)) {
            throw ValidationException::withMessages([
                'task' => 'Task tidak dapat diubah setelah Division Project diajukan ke GM atau selesai.',
            ]);
        }

        $old = [
            'status' => $task->status,
            'progress' => $task->progress,
            'completed_at' => $task->completed_at,
        ];

        $task->update([
            'status' => $completed ? Task::STATUS_DONE : Task::STATUS_TO_DO,
            'progress' => $completed ? 100 : 0,
            'completed_at' => $completed ? now() : null,
            'submitted_at' => null,
            'blocked_reason' => null,
        ]);

        $task = $task->refresh();

        $this->audit($actor, $task, $completed ? 'task.completed' : 'task.reopened', $old, [
            'status' => $task->status,
            'progress' => $task->progress,
            'completed_at' => $task->completed_at?->toISOString(),
        ]);

        return $task;
    }

    public function submitDivisionProjectForCompletion(
        DivisionProject $divisionProject,
        Employees $manager,
    ): DivisionProject {
        $this->ensureActiveEmployee($manager);

        if ((int) $divisionProject->manager_id !== (int) $manager->id) {
            throw ValidationException::withMessages([
                'manager' => 'Hanya Manager Division Project yang dapat mengajukan penyelesaian.',
            ]);
        }

        if (! in_array($divisionProject->status, [
            'draft',
            'in_progress',
            'ready_for_review',
            'manager_approved',
            'revision_required',
        ], true)) {
            throw ValidationException::withMessages([
                'division_project' => 'Division Project tidak berada pada tahap yang dapat diajukan.',
            ]);
        }

        $this->ensureAllDivisionProjectWorkCompleted($divisionProject);

        return DB::transaction(function () use ($divisionProject, $manager) {
            $old = ['status' => $divisionProject->status];

            $divisionProject->update([
                'status' => 'submitted_to_gm',
            ]);

            $this->audit($manager, $divisionProject, 'division_project.submitted_to_gm', $old, [
                'status' => $divisionProject->status,
            ]);

            $divisionProject->masterProject()->update([
                'status' => 'in_progress',
            ]);

            return $divisionProject->refresh();
        });
    }

    public function reviewDivisionProjectCompletion(
        DivisionProject $divisionProject,
        Employees $reviewer,
        string $decision,
        ?string $feedback = null,
    ): DivisionProject {
        $this->ensureActiveEmployee($reviewer);

        if (! $reviewer->user?->hasRole('general-manager')) {
            throw ValidationException::withMessages([
                'reviewer' => 'Hanya General Manager yang dapat menyetujui atau meminta revisi Division Project.',
            ]);
        }

        $masterProject = $divisionProject->masterProject()->firstOrFail();

        if ((int) $masterProject->created_by !== (int) $reviewer->id) {
            throw ValidationException::withMessages([
                'reviewer' => 'Review Division Project hanya dapat dilakukan oleh General Manager pembuat Master Project.',
            ]);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'decision' => 'Keputusan Division Project tidak valid.',
            ]);
        }

        if ($decision === 'approved') {
            if ($divisionProject->status !== 'submitted_to_gm') {
                throw ValidationException::withMessages([
                    'division_project' => 'Division Project belum diajukan Manager kepada GM.',
                ]);
            }

            $this->ensureAllDivisionProjectWorkCompleted($divisionProject);
        } elseif (! in_array($divisionProject->status, ['submitted_to_gm', 'completed'], true)) {
            throw ValidationException::withMessages([
                'division_project' => 'Division Project tidak sedang menunggu approval atau belum berstatus complete.',
            ]);
        }

        return DB::transaction(function () use (
            $divisionProject,
            $reviewer,
            $decision,
            $feedback,
        ) {
            if ($decision === 'rejected' && blank(trim((string) $feedback))) {
                throw ValidationException::withMessages([
                    'feedback' => 'Feedback revisi wajib diisi.',
                ]);
            }

            $old = ['status' => $divisionProject->status];

            $divisionProject->update([
                'status' => $decision === 'approved' ? 'completed' : 'revision_required',
            ]);

            $this->audit($reviewer, $divisionProject, $decision === 'approved'
                ? 'division_project.completed'
                : 'division_project.revision_requested', $old, [
                'status' => $divisionProject->status,
                'feedback' => $feedback,
            ]);

            $masterProject = $divisionProject->masterProject()->firstOrFail();

            if ($divisionProject->status === 'completed') {
                $this->syncMasterProjectStatus($masterProject);
            } else {
                $masterProject->update(['status' => 'in_progress']);
            }

            return $divisionProject->refresh();
        });
    }

    public function completeMasterProject(
        MasterProject $masterProject,
        Employees $reviewer,
    ): MasterProject {
        $this->ensureActiveEmployee($reviewer);

        if (! $reviewer->user?->hasRole('general-manager')) {
            throw ValidationException::withMessages([
                'reviewer' => 'Hanya General Manager yang dapat menandai Master Project selesai.',
            ]);
        }

        if ((int) $masterProject->created_by !== (int) $reviewer->id) {
            throw ValidationException::withMessages([
                'reviewer' => 'Project hanya dapat diselesaikan oleh General Manager pembuat project.',
            ]);
        }

        if (! $masterProject->divisionProjects()->exists()) {
            throw ValidationException::withMessages([
                'master_project' => 'Master Project belum memiliki Division Project.',
            ]);
        }

        $incompleteCount = $masterProject->divisionProjects()
            ->where('status', '!=', 'completed')
            ->count();

        if ($incompleteCount > 0) {
            throw ValidationException::withMessages([
                'master_project' => 'Semua Division Project harus berstatus complete sebelum project ditandai selesai.',
            ]);
        }

        return DB::transaction(function () use ($masterProject, $reviewer) {
            $old = [
                'status' => $masterProject->status,
                'approved_by' => $masterProject->approved_by,
                'approved_at' => $masterProject->approved_at,
            ];

            $masterProject->update([
                'status' => 'completed',
                'approved_by' => $reviewer->id,
                'approved_at' => now(),
            ]);

            $this->audit($reviewer, $masterProject, 'master_project.completed', $old, [
                'status' => $masterProject->status,
                'approved_by' => $masterProject->approved_by,
                'approved_at' => $masterProject->approved_at?->toISOString(),
            ]);

            return $masterProject->refresh();
        });
    }

    public function cancelTask(Task $task, Employees $actor): Task
    {
        $task->loadMissing('divisionProject', 'team');

        $this->ensureActiveEmployee($actor);

        if (! ($actor->user?->hasRole('general-manager')
            || (int) $task->divisionProject?->manager_id === (int) $actor->id
            || (int) $task->team?->supervisor_id === (int) $actor->id)) {
            throw ValidationException::withMessages([
                'actor' => 'Actor tidak memiliki scope untuk membatalkan task ini.',
            ]);
        }

        if ($task->status === Task::STATUS_CANCELLED) {
            throw ValidationException::withMessages([
                'task' => 'Task sudah dibatalkan.',
            ]);
        }

        $old = ['status' => $task->status];

        $task->update([
            'status' => Task::STATUS_CANCELLED,
            'progress' => 0,
            'completed_at' => null,
            'submitted_at' => null,
        ]);

        $task = $task->refresh();

        $this->audit($actor, $task, 'task.cancelled', $old, [
            'status' => $task->status,
        ]);

        return $task;
    }

    public function syncDivisionProjectStatus(DivisionProject $divisionProject): DivisionProject
    {
        return $divisionProject->refresh();
    }

    public function syncMasterProjectStatus(MasterProject $masterProject): MasterProject
    {
        if ($masterProject->status !== 'completed'
            && $masterProject->divisionProjects()->exists()) {
            $masterProject->update(['status' => 'in_progress']);
        }

        return $masterProject->refresh();
    }

    private function ensureAllDivisionProjectWorkCompleted(
        DivisionProject $divisionProject,
    ): void {
        $divisionProject->loadMissing('teams');

        if ($divisionProject->teams->isEmpty()) {
            throw ValidationException::withMessages([
                'division_project' => 'Division Project minimal harus memiliki satu Team.',
            ]);
        }

        foreach ($divisionProject->teams as $team) {
            $tasks = $divisionProject->tasks()
                ->where('team_id', $team->id)
                ->where('status', '!=', Task::STATUS_CANCELLED)
                ->get();

            if ($tasks->isEmpty()) {
                throw ValidationException::withMessages([
                    'division_project' => "Team {$team->name} belum memiliki task.",
                ]);
            }

            if (! $tasks->every(fn (Task $task) => $task->status === Task::STATUS_DONE)) {
                throw ValidationException::withMessages([
                    'division_project' => "Masih ada task yang belum selesai pada Team {$team->name}.",
                ]);
            }
        }
    }

    private function ensureTaskWorker(Task $task, Employees $actor): void
    {
        if ((int) $task->assignee_id !== (int) $actor->id) {
            throw ValidationException::withMessages([
                'task' => 'Hanya Task Worker yang ditugaskan yang dapat mengubah checkbox task.',
            ]);
        }

        $this->ensureTaskWorkerAccount($actor);
    }

    private function ensureTaskWorkerAccount(Employees $employee): void
    {
        if (! $employee->user?->hasRole('task-worker')
            || $employee->user?->status !== 'active') {
            throw ValidationException::withMessages([
                'employee' => 'Task Worker harus memiliki akun aktif dan role task-worker.',
            ]);
        }
    }

    private function validateTeamForDivisionProject(
        ?DivisionProject $divisionProject,
        Team $team,
        ?Divisi $division = null,
    ): void {
        if ($team->is_active !== 'active') {
            throw ValidationException::withMessages([
                'team_id' => 'Team harus aktif.',
            ]);
        }

        $division ??= $divisionProject?->division;

        if (! $division) {
            $division = Divisi::query()->find($divisionProject?->divisi_id);
        }

        if (! $division || (int) $team->divisi_id !== (int) $division->id) {
            throw ValidationException::withMessages([
                'team_id' => 'Team harus berada pada divisi yang sama dengan Division Project.',
            ]);
        }

        if (! $team->supervisor_id) {
            throw ValidationException::withMessages([
                'team_id' => 'Team harus memiliki Supervisor.',
            ]);
        }
    }

    private function ensureActiveEmployee(Employees $employee): void
    {
        if ($employee->status_employee !== 'active') {
            throw ValidationException::withMessages([
                'employee' => 'Karyawan harus aktif.',
            ]);
        }
    }

    private function audit(
        Employees $actor,
        object $auditable,
        string $action,
        ?array $oldValues,
        ?array $newValues,
        ?string $note = null,
    ): void {
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
