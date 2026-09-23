<?php

namespace App\Service;

use App\Models\DivisionProject;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\ProjectProgressUpdate;
use App\Models\ProjectReport;
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
        $this->ensureCanManageDivisionProject($divisionProject, $actor);

        if (! in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true)) {
            throw ValidationException::withMessages(['division_project' => 'Team tidak dapat ditambahkan setelah division project masuk tahap review atau approval.']);
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

        if (! in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true)) {
            throw ValidationException::withMessages(['division_project' => 'Task tidak dapat dibuat setelah division project masuk tahap review atau approval.']);
        }

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

            // Being assigned an executable task grants the stacked task-worker role.
            $assignee->user?->assignRole('employee');
            $assignee->user?->assignRole('task-worker');

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
        $this->ensureActiveEmployee($reviewer);
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
        $this->ensureActiveEmployee($reporter);

        $isManager = (int) $divisionProject->manager_id === (int) $reporter->id;
        if (! $isManager) {
            throw ValidationException::withMessages(['reporter' => 'Manual progress Division Project hanya dapat dilaporkan oleh Manager.']);
        }

        if (! in_array($divisionProject->status, [
            'draft',
            'in_progress',
            'ready_for_review',
            'submitted_to_manager',
            'manager_approved',
            'revision_required',
        ], true)) {
            throw ValidationException::withMessages(['division_project' => 'Progress manual hanya dapat diubah sebelum Division Project dikirim ke GM.']);
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

    public function submitSupervisorReport(
        DivisionProject $divisionProject,
        Employees $reporter,
        string $content,
    ): ProjectReport {
        $this->ensureActiveEmployee($reporter);
        $divisionProject->loadMissing('teams');

        if (! in_array($divisionProject->status, ['in_progress', 'ready_for_review', 'revision_required'], true)) {
            throw ValidationException::withMessages([
                'division_project' => 'Division project sudah masuk tahap review atau approval dan tidak menerima laporan Supervisor baru.',
            ]);
        }

        $team = $divisionProject->teams
            ->first(fn (Team $team) => (int) $team->supervisor_id === (int) $reporter->id);

        if (! $team) {
            throw ValidationException::withMessages([
                'reporter' => 'Supervisor tidak memiliki Team yang ditugaskan pada division project ini.',
            ]);
        }

        $tasks = $divisionProject->tasks()
            ->where('team_id', $team->id)
            ->where('status', '!=', Task::STATUS_CANCELLED)
            ->get();

        if ($tasks->isEmpty() || ! $tasks->every(fn (Task $task) => $task->status === Task::STATUS_DONE)) {
            throw ValidationException::withMessages([
                'division_project' => 'Semua task aktif pada Team harus selesai dan disetujui Supervisor sebelum laporan dikirim.',
            ]);
        }

        if (blank(trim($content))) {
            throw ValidationException::withMessages([
                'content' => 'Laporan Supervisor wajib diisi.',
            ]);
        }

        return DB::transaction(function () use ($divisionProject, $reporter, $team, $content) {
            $report = $divisionProject->reports()->create([
                'team_id' => $team->id,
                'reported_by' => $reporter->id,
                'report_level' => ProjectReport::LEVEL_SUPERVISOR,
                'status' => ProjectReport::STATUS_SUBMITTED,
                'content' => trim($content),
            ]);

            $this->audit($reporter, $report, 'division_project.supervisor_report_submitted', null, [
                'division_project_id' => $divisionProject->id,
                'team_id' => $team->id,
                'status' => $report->status,
            ]);

            $this->syncSupervisorReportsStatus($divisionProject);

            return $report->refresh();
        });
    }

    public function reviewTeamReport(
        DivisionProject $divisionProject,
        Team $team,
        Employees $reviewer,
        string $decision,
        ?string $feedback = null,
    ): ProjectReview {
        $this->ensureActiveEmployee($reviewer);

        if ((int) $divisionProject->manager_id !== (int) $reviewer->id) {
            throw ValidationException::withMessages([
                'reviewer' => 'Hanya manager division project yang dapat melakukan review laporan Supervisor.',
            ]);
        }

        if (! $divisionProject->teams()->whereKey($team->id)->exists()) {
            throw ValidationException::withMessages([
                'team' => 'Team belum ditugaskan pada division project ini.',
            ]);
        }

        if ($divisionProject->status !== 'submitted_to_manager') {
            throw ValidationException::withMessages([
                'division_project' => 'Semua Supervisor harus mengirim laporan sebelum Manager melakukan review.',
            ]);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'decision' => 'Decision review tidak valid.',
            ]);
        }

        $report = $divisionProject->reports()
            ->where('report_level', ProjectReport::LEVEL_SUPERVISOR)
            ->where('team_id', $team->id)
            ->where('status', ProjectReport::STATUS_SUBMITTED)
            ->latest('id')
            ->first();

        if (! $report) {
            throw ValidationException::withMessages([
                'report' => 'Belum ada laporan Supervisor yang menunggu review untuk Team ini.',
            ]);
        }

        return DB::transaction(function () use ($divisionProject, $team, $reviewer, $decision, $feedback, $report) {
            $review = $divisionProject->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'reviewer_level' => 'manager',
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $old = ['status' => $report->status];

            $report->update([
                'status' => $decision === 'approved'
                    ? ProjectReport::STATUS_APPROVED
                    : ProjectReport::STATUS_REJECTED,
            ]);

            if ($decision === 'rejected') {
                $divisionProject->update(['status' => 'revision_required']);
            } else {
                $this->syncManagerApprovalStatus($divisionProject);
            }

            $this->audit($reviewer, $report, 'division_project.supervisor_report_reviewed', $old, [
                'status' => $report->status,
                'decision' => $decision,
                'feedback' => $feedback,
                'team_id' => $team->id,
            ]);

            return $review;
        });
    }

    private function syncManagerApprovalStatus(DivisionProject $divisionProject): DivisionProject
    {
        $teams = $divisionProject->teams()->get();

        if ($teams->isEmpty()) {
            return $divisionProject;
        }

        $allApproved = $teams->every(function (Team $team) use ($divisionProject) {
            $latest = $divisionProject->reports()
                ->where('report_level', ProjectReport::LEVEL_SUPERVISOR)
                ->where('team_id', $team->id)
                ->latest('id')
                ->first();

            return $latest?->status === ProjectReport::STATUS_APPROVED;
        });

        if ($allApproved) {
            $divisionProject->update(['status' => 'manager_approved']);
        }

        return $divisionProject->refresh();
    }

    public function submitDivisionProjectToGM(
        DivisionProject $divisionProject,
        Employees $reporter,
        string $content,
    ): ProjectReport {
        $this->ensureActiveEmployee($reporter);

        if ((int) $divisionProject->manager_id !== (int) $reporter->id) {
            throw ValidationException::withMessages([
                'reporter' => 'Hanya manager division project yang dapat melaporkan hasil ke General Manager.',
            ]);
        }

        if (! in_array($divisionProject->status, ['manager_approved', 'revision_required'], true)) {
            throw ValidationException::withMessages([
                'division_project' => 'Division project harus disetujui manager atau sedang dalam tahap revisi sebelum dilaporkan ke General Manager.',
            ]);
        }

        $existingSubmission = $divisionProject->reports()
            ->where('report_level', ProjectReport::LEVEL_MANAGER)
            ->where('status', ProjectReport::STATUS_SUBMITTED)
            ->exists();

        if ($existingSubmission) {
            throw ValidationException::withMessages([
                'division_project' => 'Laporan Manager masih menunggu review General Manager.',
            ]);
        }

        if (blank(trim($content))) {
            throw ValidationException::withMessages([
                'content' => 'Laporan Manager wajib diisi.',
            ]);
        }

        $progress = (int) $divisionProject->manual_progress;

        return DB::transaction(function () use ($divisionProject, $reporter, $content, $progress) {
            $report = $divisionProject->reports()->create([
                'team_id' => null,
                'reported_by' => $reporter->id,
                'report_level' => ProjectReport::LEVEL_MANAGER,
                'status' => ProjectReport::STATUS_SUBMITTED,
                'content' => trim($content),
                'progress' => $progress,
            ]);

            $divisionProject->update(['status' => 'submitted_to_gm']);

            $this->audit($reporter, $report, 'division_project.manager_report_submitted_to_gm', null, [
                'division_project_id' => $divisionProject->id,
                'progress' => $progress,
                'status' => $report->status,
            ]);

            $this->syncMasterProjectStatus($divisionProject->masterProject()->firstOrFail());

            return $report->refresh();
        });
    }

    public function reviewManagerReport(
        DivisionProject $divisionProject,
        Employees $reviewer,
        string $decision,
        ?string $feedback = null,
    ): ProjectReview {
        $this->ensureActiveEmployee($reviewer);

        $masterProject = $divisionProject->masterProject()->firstOrFail();

        if (! $reviewer->user?->hasRole('general-manager')) {
            throw ValidationException::withMessages([
                'reviewer' => 'Hanya General Manager yang dapat mereview laporan Manager.',
            ]);
        }

        if ((int) $masterProject->created_by !== (int) $reviewer->id) {
            throw ValidationException::withMessages([
                'reviewer' => 'Review laporan Manager hanya dapat dilakukan oleh General Manager yang membuat Master Project.',
            ]);
        }

        if ($divisionProject->status !== 'submitted_to_gm') {
            throw ValidationException::withMessages([
                'division_project' => 'Division Project belum memiliki laporan Manager yang menunggu review GM.',
            ]);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'decision' => 'Decision review tidak valid.',
            ]);
        }

        $report = $divisionProject->reports()
            ->where('report_level', ProjectReport::LEVEL_MANAGER)
            ->where('status', ProjectReport::STATUS_SUBMITTED)
            ->latest('id')
            ->first();

        if (! $report) {
            throw ValidationException::withMessages([
                'report' => 'Belum ada laporan Manager yang menunggu review GM.',
            ]);
        }

        return DB::transaction(function () use ($divisionProject, $reviewer, $decision, $feedback, $report, $masterProject) {
            $review = $divisionProject->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'reviewer_level' => 'general_manager',
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            $old = [
                'status' => $report->status,
                'progress' => $report->progress,
            ];

            $report->update([
                'status' => $decision === 'approved'
                    ? ProjectReport::STATUS_APPROVED
                    : ProjectReport::STATUS_REJECTED,
            ]);

            if ($decision === 'rejected') {
                $divisionProject->update(['status' => 'revision_required']);
                $masterProject->update(['status' => 'in_progress']);
            } else {
                $this->syncMasterProjectStatus($masterProject);
            }

            $this->audit($reviewer, $report, 'division_project.manager_report_reviewed_by_gm', $old, [
                'status' => $report->status,
                'progress' => $report->progress,
                'decision' => $decision,
                'feedback' => $feedback,
            ]);

            return $review;
        });
    }

    public function reviewMasterProject(MasterProject $masterProject, Employees $reviewer, string $decision, ?string $feedback = null): ProjectReview
    {
        $this->ensureActiveEmployee($reviewer);

        if (! $reviewer->user?->hasRole('general-manager')) {
            throw ValidationException::withMessages(['reviewer' => 'Hanya General Manager yang dapat melakukan final approval.']);
        }

        if ((int) $masterProject->created_by !== (int) $reviewer->id) {
            throw ValidationException::withMessages(['reviewer' => 'Final approval hanya dapat dilakukan oleh General Manager yang membuat Master Project.']);
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

            if ($decision === 'approved') {
                $masterProject->divisionProjects()
                    ->where('status', 'submitted_to_gm')
                    ->update(['status' => 'completed']);

                $masterProject->divisionProjects()
                    ->whereHas('reports', function ($query) {
                        $query
                            ->where('report_level', ProjectReport::LEVEL_MANAGER)
                            ->where('status', ProjectReport::STATUS_SUBMITTED);
                    })
                    ->get()
                    ->each(function (DivisionProject $divisionProject) {
                        $divisionProject->reports()
                            ->where('report_level', ProjectReport::LEVEL_MANAGER)
                            ->where('status', ProjectReport::STATUS_SUBMITTED)
                            ->update(['status' => ProjectReport::STATUS_APPROVED]);
                    });
            } else {
                $masterProject->divisionProjects()
                    ->where('status', 'submitted_to_gm')
                    ->update(['status' => 'revision_required']);

                $masterProject->divisionProjects()
                    ->whereHas('reports', function ($query) {
                        $query
                            ->where('report_level', ProjectReport::LEVEL_MANAGER)
                            ->where('status', ProjectReport::STATUS_SUBMITTED);
                    })
                    ->get()
                    ->each(function (DivisionProject $divisionProject) {
                        $divisionProject->reports()
                            ->where('report_level', ProjectReport::LEVEL_MANAGER)
                            ->where('status', ProjectReport::STATUS_SUBMITTED)
                            ->update(['status' => ProjectReport::STATUS_REJECTED]);
                    });
            }

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

        if ($divisionProject->allRequiredWorkCompleted()
            && in_array($divisionProject->status, ['in_progress', 'revision_required', 'ready_for_review'], true)) {
            $divisionProject->update(['status' => 'ready_for_review']);
        } elseif ($divisionProject->tasks->isNotEmpty()
            && ! in_array($divisionProject->status, [
                'in_progress',
                'revision_required',
                'ready_for_review',
                'submitted_to_manager',
                'manager_approved',
                'submitted_to_gm',
            ], true)) {
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

        $allRequiredManagerReportsApproved = $requiredProjects->isNotEmpty()
            && $requiredProjects->every(function (DivisionProject $project) {
                $latest = $project->reports()
                    ->where('report_level', ProjectReport::LEVEL_MANAGER)
                    ->latest('id')
                    ->first();

                return $latest?->status === ProjectReport::STATUS_APPROVED;
            });

        if ($allRequiredManagerReportsApproved) {
            $masterProject->update(['status' => 'ready_for_review']);
        } elseif ($masterProject->status === 'ready_for_review') {
            $masterProject->update(['status' => 'in_progress']);
        }

        return $masterProject->refresh();
    }

    private function syncSupervisorReportsStatus(DivisionProject $divisionProject): void
    {
        $teams = $divisionProject->teams()->get();

        if ($teams->isEmpty()) {
            return;
        }

        $allReported = $teams->every(function (Team $team) use ($divisionProject) {
            $latest = $divisionProject->reports()
                ->where('report_level', ProjectReport::LEVEL_SUPERVISOR)
                ->where('team_id', $team->id)
                ->latest('id')
                ->first();

            return in_array($latest?->status, [
                ProjectReport::STATUS_SUBMITTED,
                ProjectReport::STATUS_APPROVED,
            ], true);
        });

        if ($allReported) {
            $divisionProject->update(['status' => 'submitted_to_manager']);
        } else {
            $divisionProject->update(['status' => 'in_progress']);
        }
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
