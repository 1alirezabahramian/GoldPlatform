<?php

namespace App\Console\Commands;

use App\Integrations\Kimia\Client\KimiaClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('kimia:test')]
#[Description('Test connection to Kimia API')]
class TestKimiaConnection extends Command
{
    public function __construct(
        protected KimiaClient $client
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Connecting to Kimia API...');

        try {
            $response = $this->client->get('/swagger/v1/swagger.json');

            $this->newLine();
            $this->info('HTTP Status : '.$response->status());
            $this->newLine();
            $this->info('Connection Successful.');
            $this->newLine();
            $this->line(substr($response->body(), 0, 500));
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
