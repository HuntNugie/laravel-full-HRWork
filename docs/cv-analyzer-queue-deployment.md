# CV Analyzer Queue Deployment

This change moves the long-running CV analysis request into a database-backed Laravel queue. The Livewire request stores the uploaded CV privately, creates a cv_analysis_runs row, dispatches AnalyzeCvJob, and returns without waiting for the AI provider. The UI then polls the current user's run status.

## What changes

- The upload/validation rules and existing analysis result schema are retained.
- Source files are saved to the private local disk under cv-analysis/.
- The queue worker extracts CV text and requests the AI provider.
- Analysis runs are scoped to their owner when status is polled.
- Source files are deleted after completion, handled failure, or cancellation.
- The AI provider request timeout is 540 seconds. The queue worker timeout is 600 seconds and the database queue's retry_after default is 660 seconds. Keep retry_after longer than the worker timeout to avoid the same job being reserved again while it is still running.
- bootstrap/app.php trusts proxy headers only from loopback addresses for deployments where Cloudflare Tunnel/Nginx connects to PHP through the local origin. The deployment's .env must also use the public HTTPS APP_URL.

## Required environment

Set the values appropriate for your deployment in .env:

    APP_URL=https://hris.huntnugie.my.id
    QUEUE_CONNECTION=database
    DB_QUEUE_RETRY_AFTER=660

Keep the OpenKey/provider URL and API key configured in the environment as usual. Do not commit production credentials.

This project already has Laravel's jobs, job_batches, and failed_jobs tables in the base migration. Run the new migration to create cv_analysis_runs.

## Local development

Pull this branch and install/update dependencies if needed:

    git fetch origin
    git switch feat/cv-analyzer-background-queue
    git pull --ff-only origin feat/cv-analyzer-background-queue
    composer install
    php artisan migrate
    php artisan optimize:clear

In a separate terminal, start the worker:

    php artisan queue:work database --queue=cv-analysis --sleep=3 --tries=1 --timeout=600

Keep the app and queue worker running in separate terminals. If the worker is not running, the web request should still enqueue the analysis, but it will remain queued until a worker processes it.

## Production with systemd

Adapt WorkingDirectory, ExecStart, User, and Group in deploy/systemd/hrwork-cv-analysis-worker.service if your deployment path or Unix account differs.

    cd /var/www/hrwork
    git fetch origin
    git switch feat/cv-analyzer-background-queue
    git pull --ff-only origin feat/cv-analyzer-background-queue
    composer install --no-dev --optimize-autoloader
    php artisan migrate --force
    php artisan optimize:clear

Install and start the worker service:

    sudo cp deploy/systemd/hrwork-cv-analysis-worker.service /etc/systemd/system/hrwork-cv-analysis-worker.service
    sudo systemctl daemon-reload
    sudo systemctl enable --now hrwork-cv-analysis-worker
    sudo systemctl status hrwork-cv-analysis-worker --no-pager

Follow worker logs:

    sudo journalctl -u hrwork-cv-analysis-worker -f

Restart the worker after deploying code changes:

    sudo systemctl restart hrwork-cv-analysis-worker

## Logs and diagnostics

- Laravel exceptions: tail -f storage/logs/laravel.log
- Worker output: sudo journalctl -u hrwork-cv-analysis-worker -f
- Failed jobs: php artisan queue:failed
- Run status is stored in cv_analysis_runs.

The queue removes the long AI request from the browser's HTTP request, which avoids keeping the Cloudflare Tunnel request open throughout analysis. It does not make a slow/unavailable AI provider faster; provider errors are stored as a failed analysis.

## HTTPS behind Cloudflare Tunnel

If Livewire requests are rendered as http:// while the public site is https://, verify that APP_URL uses HTTPS and that the origin proxy forwards the original X-Forwarded-Proto header to PHP. The Laravel proxy configuration in this branch trusts those headers only when the request arrives from loopback. If Nginx is in front of PHP-FPM, ensure its PHP location passes the header through, for example:

    fastcgi_param HTTP_X_FORWARDED_PROTO $http_x_forwarded_proto;

After a configuration change, run php artisan optimize:clear and reload the page. Only trust proxy addresses that are genuinely controlled by your deployment.
