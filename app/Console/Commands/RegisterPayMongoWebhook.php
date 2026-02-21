<?php

namespace App\Console\Commands;

use App\Services\PayMongoService;
use Illuminate\Console\Command;

class RegisterPayMongoWebhook extends Command
{
    protected $signature = 'paymongo:register-webhook {--url= : The webhook URL (defaults to APP_URL/webhooks/paymongo)}';
    protected $description = 'Register a webhook endpoint with PayMongo';

    public function handle(PayMongoService $paymongo): int
    {
        $url = $this->option('url') ?: url('/webhooks/paymongo');

        $this->info("Registering webhook: {$url}");

        $result = $paymongo->createWebhook($url, [
            'checkout_session.payment.paid',
            'link.payment.paid',
        ]);

        if ($result) {
            $this->info('Webhook registered successfully!');
            $this->info("Webhook ID: {$result['id']}");
            $this->info("Secret Key: {$result['attributes']['secret_key']}");
            $this->newLine();
            $this->warn('Add this to your .env file:');
            $this->line("PAYMONGO_WEBHOOK_SECRET={$result['attributes']['secret_key']}");
            return 0;
        }

        $this->error('Failed to register webhook. Check your PayMongo secret key.');
        return 1;
    }
}
