<?php

namespace App\Http\Controllers;

use App\Models\DivisionProject;
use App\Models\WorkManagementAudit;
use Spatie\LaravelPdf\Facades\Pdf;

class PrintDivisionProjectCompletionController extends Controller
{
    public function __invoke(DivisionProject $divisionProject)
    {
        abort_unless(
            auth()->user()?->hasRole('general-manager'),
            403
        );

        abort_unless($divisionProject->status === 'completed', 404);

        $divisionProject->load([
            'masterProject.creator.user',
            'division',
            'manager.user',
            'teams.supervisor.user',
            'teams.employees',
            'tasks.team',
        ]);

        $completionAudit = WorkManagementAudit::query()
            ->with('actor.user')
            ->where('auditable_type', DivisionProject::class)
            ->where('auditable_id', $divisionProject->id)
            ->where('action', 'division_project.completed')
            ->latest('created_at')
            ->first();

        $approver = $completionAudit?->actor ?? $divisionProject->masterProject?->creator;
        $completionDate = $completionAudit?->created_at ?? $divisionProject->updated_at;

        $filename = 'berita-acara-penyelesaian-' . $this->safeFilename($divisionProject->name) . '.pdf';

        return Pdf::view('print.work-management.division-project-completion', [
            'divisionProject' => $divisionProject,
            'approver' => $approver,
            'completionDate' => $completionDate,
        ])
            ->driver('chrome')
            ->format('a4')
            ->orientation('portrait')
            ->name($filename);
    }

    private function safeFilename(string|int $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $value);

        return trim($value, '-') ?: 'division-project';
    }
}
