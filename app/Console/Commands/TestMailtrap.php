<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailtrap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : The email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify Mailtrap configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter email address to send test email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return 1;
        }

        $this->info('Sending test email to: ' . $email);

        try {
            Mail::raw('This is a test email from your ERP system. Mailtrap is configured correctly!', function ($message) use ($email) {
                $message->to($email)
                    ->subject('🎉 Mailtrap Test Email - Success!');
            });

            $this->info('✅ Test email sent successfully!');
            $this->line('Check your Mailtrap inbox at: https://mailtrap.io/inboxes');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
