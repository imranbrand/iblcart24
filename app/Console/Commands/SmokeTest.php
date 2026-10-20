<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * Quick "does the live site actually open?" test. Read-only: it never creates orders.
 *
 *   php artisan shop:smoke-test
 *   php artisan shop:smoke-test --sms=01XXXXXXXXX     (also sends ONE real test SMS)
 */
class SmokeTest extends Command
{
    protected $signature = 'shop:smoke-test {--sms= : Phone number that should receive one test SMS}';

    protected $description = 'Open the main pages of the shop and report any that fail';

    /** route name => label */
    protected array $pages = [
        'home'            => 'Home page',
        'products.index'  => 'Products list',
        'cart.index'      => 'Cart',
        'checkout.index'  => 'Checkout',
        'login'           => 'Login',
        'register'        => 'Register',
        'pages.about'     => 'About',
        'pages.contact'   => 'Contact',
        'pages.privacy'   => 'Privacy Policy',
        'pages.terms'     => 'Terms',
        'pages.returns'   => 'Returns',
        'pages.shipping'  => 'Shipping',
        'pages.faq'       => 'FAQ',
        'sitemap'         => 'sitemap.xml',
    ];

    public function handle(): int
    {
        $failed = false;
        $rows   = [];
        $kernel = app(Kernel::class);

        foreach ($this->pages as $name => $label) {
            if (! Route::has($name)) {
                $rows[] = [$label, '-', 'SKIP (no such route)'];

                continue;
            }

            $path = route($name, [], false);

            try {
                $response = $kernel->handle(Request::create($path, 'GET'));
                $code     = $response->getStatusCode();
                $ok       = $code < 400;
            } catch (Throwable $e) {
                $code = 'ERR';
                $ok   = false;
                $this->line('  ' . $label . ': ' . $e->getMessage());
            }

            if (! $ok) {
                $failed = true;
            }

            $rows[] = [$label, $path, $ok ? 'OK (' . $code . ')' : 'FAIL (' . $code . ')'];
        }

        $this->table(['Page', 'Path', 'Result'], $rows);

        $phone = $this->option('sms');

        if ($phone) {
            $sent = app(SmsService::class)->send($phone, config('app.name') . ': test SMS. Your shop can send messages.');
            $driver = config('logistics.sms.driver');

            if ($sent) {
                $this->info("SMS accepted (driver: {$driver})." . ($driver === 'log' ? ' Driver is "log", so nothing was really sent. Check storage/logs.' : ' Check your phone.'));
            } else {
                $this->error('SMS failed. Check SMS_API_KEY, SMS_SENDER_ID and the number. See storage/logs.');
                $failed = true;
            }
        }

        if ($failed) {
            $this->error('Some checks failed. Open storage/logs/laravel.log for the details.');

            return self::FAILURE;
        }

        $this->info('All pages opened without errors.');

        return self::SUCCESS;
    }
}
