# CV Analyzer Background Queue Deployment

The CV Analyzer now dispatches the expensive PDF/DOCX/TXT extraction and AI request to the database queue. Its Livewire request only validates and saves the upload, creates a `cv_analysis_runs` row, and dispatches `App\Jobs\AnalyzeCvJob`.

## Deploy

Run from the deployed project directory:

```bash
git fetch origin
git switch feat/cv-analyzer-background-queue
git pull --ff-only origin feat/cv-analyzer-background-queue
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
npm run build
```

The migration is additive; it creates `cv_analysis_runs` and does not drop existing tables or data. Do not run `migrate:fresh`.

Ensure the existing `jobs` and `failed_jobs` tables are present because the application uses `QUEUE_CONNECTION=database`. Run `php artisan queue:failed-table` only if the failed jobs migration is absent, then run `php artisan migrate --force`.

## Worker

The analysis job uses the `cv-analysis` queue, has a 240-second job timeout, and requires the worker timeout to be 240 seconds. The database queue's `retry_after` is configured to 300 seconds so a long job is not reserved twice. Keep the worker timeout below `retry_after`.

Start a worker for a smoke test:

```bash
php artisan queue:work database --queue=cv-analysis --timeout=240 --tries=1
```

For production, run that command under a dedicated systemd service and set its `WorkingDirectory` to the deployed HRWork project directory. After code deployment, restart the worker with `php artisan queue:restart`; a long-running worker must be restarted to load new code.

## Flow

1. Livewire validates the upload and stores it on the private local disk.
2. A row with `status=queued` is created and the job is dispatched.
3. The browser polls `refreshAnalysisStatus` through short Livewire requests.
4. The worker extracts text, calls the configured AI provider, validates the JSON response, then persists `completed` or `failed`.
5. The uploaded private CV is removed after the job reaches a terminal status.

The CV is kept on private local storage. If multiple app servers are introduced later, move the file to shared object storage and update the disk configuration because a database queue alone does not share local files across machines.
