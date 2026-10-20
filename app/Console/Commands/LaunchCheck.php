<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class LaunchCheck extends Command
{
    protected $signature = 'shop:launch-check';

    protected $description = 'Check that the shop is ready to go live (debug mode, mail, SMS, courier, cron, backups, products)';

    public function handle(): int
    {
        $rows   = [];
        $failed = false;

        $add = function (string $label, string $level, string $fix = '') use (&$rows, &$failed) {
            $rows[] = [$label, $level, $fix];

            if ($level === 'FAIL') {
                $failed = true;
            }
        };

        $add(
            'APP_DEBUG is off',
            config('app.debug') ? 'FAIL' : 'OK',
            'Set APP_DEBUG=false in the live .env (debug mode shows secrets on error pages).'
        );

        $add(
            'APP_ENV is production',
            config('app.env') === 'production' ? 'OK' : 'WARN',
            'Set APP_ENV=production on the live server.'
        );

        $add(
            'APP_URL uses https',
            str_starts_with((string) config('app.url'), 'https://') ? 'OK' : 'WARN',
            'Set APP_URL=https://your-domain.com once SSL is installed.'
        );

        $add(
            'APP_KEY is set',
            config('app.key') ? 'OK' : 'FAIL',
            'Run: php artisan key:generate'
        );

        try {
            DB::connection()->getPdo();
            $add('Database connection', 'OK');
        } catch (Throwable $e) {
            $add('Database connection', 'FAIL', 'Check DB_* values in .env.');
        }

        $mailer = (string) config('mail.default');
        $add(
            'Real email is configured',
            in_array($mailer, ['log', 'array'], true) ? 'WARN' : 'OK',
            'Customers will not receive emails. Set MAIL_MAILER=smtp and your SMTP details.'
        );

        $from = (string) config('mail.from.address');
        $add(
            'Sender email address',
            ($from === '' || str_contains($from, 'example.com')) ? 'WARN' : 'OK',
            'Set MAIL_FROM_ADDRESS to your own email.'
        );

        $add(
            'Public storage link',
            file_exists(public_path('storage')) ? 'OK' : 'WARN',
            'Run: php artisan storage:link'
        );

        $logFile = storage_path('logs/laravel.log');
        $logSize = file_exists($logFile) ? filesize($logFile) : 0;
        $add(
            'Log file size',
            $logSize > 20 * 1024 * 1024 ? 'WARN' : 'OK',
            'laravel.log is ' . number_format($logSize / 1048576, 1) . ' MB. Clear it or set LOG_STACK=daily.'
        );

        $add(
            'Admin account exists',
            User::where('is_admin', true)->exists() ? 'OK' : 'FAIL',
            'Run: php artisan admin:promote your-email@example.com'
        );

        $add(
            'Shop phone / WhatsApp set',
            (config('shop.phone') || config('shop.whatsapp')) ? 'OK' : 'WARN',
            'Fill Admin > Settings (or SHOP_PHONE / SHOP_WHATSAPP in .env).'
        );

        $add(
            'SMS sends real messages',
            config('logistics.sms.driver') === 'log' ? 'WARN' : 'OK',
            'Set SMS_DRIVER=bulksmsbd with SMS_API_KEY and SMS_SENDER_ID.'
        );

        $add(
            'Courier (Steadfast) connected',
            (new \App\Services\SteadfastService())->enabled() ? 'OK' : 'WARN',
            'Set STEADFAST_ENABLED=true with STEADFAST_API_KEY and STEADFAST_SECRET_KEY.'
        );

        $dir       = (string) config('logistics.backup.path');
        $recent    = false;
        $threshold = now()->subDays(2)->getTimestamp();

        if (File::isDirectory($dir)) {
            foreach (File::files($dir) as $backup) {
                if (str_starts_with($backup->getFilename(), 'db-') && $backup->getMTime() >= $threshold) {
                    $recent = true;
                    break;
                }
            }
        }

        $add(
            'Database backup in the last 2 days',
            $recent ? 'OK' : 'WARN',
            'Run: php artisan shop:backup  (and schedule php artisan schedule:run every minute).'
        );

        // ---- Added for go-live (Batch 6) ----

        $add(
            'Config is cached (faster site)',
            app()->configurationIsCached() ? 'OK' : 'WARN',
            'Run: php artisan config:cache   (run it again after every .env change).'
        );

        $tablesReady = Schema::hasTable('orders')
            && Schema::hasTable('blocked_phones')
            && Schema::hasTable('abandoned_carts');
        $add(
            'Database tables are up to date',
            $tablesReady ? 'OK' : 'FAIL',
            'Run: php artisan migrate --force'
        );

        $productCount = 0;
        try {
            $productCount = Product::count();
        } catch (Throwable $e) {
            // table missing - already reported above
        }
        $add(
            'Shop has products',
            $productCount > 0 ? 'OK' : 'WARN',
            'Add your real products in Admin > Products before opening the shop.'
        );

        $beat = (int) Cache::get('scheduler:heartbeat', 0);
        $add(
            'Cron / scheduler is running',
            ($beat > 0 && now()->getTimestamp() - $beat <= 300) ? 'OK' : 'WARN',
            'Backups and reminder SMS need a cron job every minute: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1  (wait 2 minutes, then run this check again).'
        );

        $add(
            'Queue does not need a worker',
            config('queue.default') === 'database' ? 'WARN' : 'OK',
            'QUEUE_CONNECTION=database needs "php artisan queue:work" running. Use QUEUE_CONNECTION=sync on simple hosting.'
        );

        $add(
            'Images become WebP (GD installed)',
            function_exists('imagewebp') ? 'OK' : 'WARN',
            'Enable the GD extension in php.ini. Without it photos still upload, just not optimized.'
        );

        $add(
            'Secure session cookie on https',
            (str_starts_with((string) config('app.url'), 'https://') && ! config('session.secure')) ? 'WARN' : 'OK',
            'Set SESSION_SECURE_COOKIE=true in the live .env.'
        );

        $abandonedOn = (bool) config('shop.abandoned_cart.enabled', true);
        $smsReal     = config('logistics.sms.driver') !== 'log';
        $urlReal     = ! str_contains((string) config('app.url'), 'localhost') && ! str_contains((string) config('app.url'), '127.0.0.1');
        $add(
            'Abandoned-cart SMS can work',
            (! $abandonedOn || ($smsReal && $urlReal)) ? 'OK' : 'WARN',
            'The reminder SMS contains a link: needs SMS_DRIVER=bulksmsbd and APP_URL set to your real domain.'
        );

        $add(
            'Facebook Pixel / Google Analytics',
            (config('shop.fb_pixel_id') || config('shop.ga_id')) ? 'OK' : 'WARN',
            'Optional but important for Facebook ads: set FB_PIXEL_ID and GA_MEASUREMENT_ID.'
        );

        $sslReady = (string) config('services.sslcommerz.store_id') !== ''
            && ! str_contains(strtolower((string) config('services.sslcommerz.store_id')), 'your');

        if (config('shop.online_payment_enabled', true)) {
            $add(
                'Online Payment (SSLCommerz)',
                $sslReady ? 'OK' : 'FAIL',
                'Online Payment is shown at checkout but SSLCommerz is not set up. Set ONLINE_PAYMENT_ENABLED=false until it is live.'
            );
        } else {
            $add('Online Payment (SSLCommerz)', 'SKIP', 'Hidden at checkout (ONLINE_PAYMENT_ENABLED=false). Turn on after SSLCommerz is live.');
        }

        $this->table(['Check', 'Result', 'How to fix'], array_map(
            fn ($row) => [$row[0], $row[1], $row[1] === 'OK' ? '' : $row[2]],
            $rows
        ));

        if ($failed) {
            $this->error('Not ready: fix the FAIL items before going live.');

            return self::FAILURE;
        }

        $this->info('No blocking problems. Review the WARN items if any.');

        return self::SUCCESS;
    }
}
