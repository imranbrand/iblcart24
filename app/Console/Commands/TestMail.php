<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'shop:test-mail {email : Address that should receive the test email}';

    protected $description = 'Send a test email to check your mail (SMTP) settings.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $this->line('Mailer : ' . config('mail.default'));
        $this->line('Host   : ' . config('mail.mailers.smtp.host'));
        $this->line('Port   : ' . config('mail.mailers.smtp.port'));
        $this->line('From   : ' . config('mail.from.address'));

        if (config('mail.default') === 'log') {
            $this->warn('MAIL_MAILER is "log": emails are only written to storage/logs/laravel.log and never reach a real inbox.');
        }

        try {
            Mail::raw(
                'This is a test email from ' . config('app.name') . '. If you can read this, your mail settings work.',
                function ($message) use ($email) {
                    $message->to($email)->subject(config('app.name') . ' - test email');
                }
            );
        } catch (\Throwable $e) {
            $this->error('Sending failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info('Test email sent to ' . $email . '. Check the inbox (and spam folder).');

        return self::SUCCESS;
    }
}
