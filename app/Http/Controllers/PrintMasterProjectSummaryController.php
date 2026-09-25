<?php

namespace App\Http\Controllers;

use App\Models\MasterProject;
use Spatie\LaravelPdf\Facades\Pdf;

class PrintMasterProjectSummaryController extends Controller
{
    public function __invoke(MasterProject $masterProject)
    {
        abort_unless(
            auth()->user()?->hasRole('general-manager'),
            403
        );

        abort_unless($masterProject->status === 'completed', 404);

        $masterProject->load([
            'creator.user',
            'approver.user',
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.teams.supervisor.user',
            'divisionProjects.teams.employees',
            'divisionProjects.tasks',
        ]);

        $completionDate = $masterProject->approved_at ?? $masterProject->updated_at;

        $filename = 'rekap-master-project-' . $this->safeFilename($masterProject->name) . '.pdf';

        return Pdf::view('print.work-management.master-project-summary', [
            'masterProject' => $masterProject,
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

        return trim($value, '-') ?: 'master-project';
    }
}
