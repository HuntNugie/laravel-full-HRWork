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

Ensure the existing `jobs` and `failed_jobs` tables are present because the application uses `QUEUE_CONNECTION=database`. The stock repository has a migration for both tables. If your deployed database was migrated from an older history and one is missing, add the appropriate missing-table migration before running `php artisan migrate --force`; do not rerun an already-applied migration.

## Worker

The analysis job uses the `cv-analysis` queue, has a 240-second job timeout, and requires the worker timeout to be 240 seconds. The database queue's `retry_after` is configured to 300 seconds so a long job is not reserved twice. Keep the worker timeout below `retry_after`.

Start a worker for a smoke test:

```bash
php artisan queue:work database --queue=cv-analysis --timeout=240 --tries=1
```

A systemd unit example is included at `deploy/systemd/hrwork-cv-analysis-worker.service`. Review its `WorkingDirectory` and `User`/group before installing: the worker needs read access to application code and write access to `storage` and `bootstrap/cache`. If the project is owned by `huntnugie` and access is managed with ACLs, you can run the worker as `huntnugie`; alternatively, keep it as `www-data` only if the required ACLs are present.

Install and start the service after deploying the code and migration:

```bash
sudo cp deploy/systemd/hrwork-cv-analysis-worker.service /etc/systemd/system/hrwork-cv-analysis-worker.service
sudo systemctl daemon-reload
sudo systemctl enable --now hrwork-cv-analysis-worker
sudo systemctl status hrwork-cv-analysis-worker --no-pager
```

Check worker output with `sudo journalctl -u hrwork-cv-analysis-worker -f`. Before enabling the unit, review its `User`, `Group`, `WorkingDirectory`, and PHP binary. The chosen user must be able to read the project and write to `storage` and `bootstrap/cache`. After future code deployments, restart the worker with `php artisan queue:restart`; a long-running worker must restart to load new code.

## Flow

1. Livewire validates the upload and stores it on the private local disk.
2. A row with `status=queued` is created and the job is dispatched.
3. The browser polls `refreshAnalysisStatus` through short Livewire requests.
4. The worker extracts text, calls the configured AI provider, validates the JSON response, then persists `completed` or `failed`.
5. The uploaded private CV is removed after the job reaches a terminal status.

The CV is kept on private local storage. If multiple app servers are introduced later, move the file to shared object storage and update the disk configuration because a database queue alone does not share local files across machines.
