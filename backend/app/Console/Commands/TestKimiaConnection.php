<?php

namespace App\Console\Commands;

use App\Services\KimiaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('kimia:test')]
#[Description('Test connection to Kimia API')]
class TestKimiaConnection extends Command
{
    public function __construct(
        protected KimiaService $kimiaService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Connecting to Kimia API...');

        try {

            $response = $this->kimiaService->test();

            $this->line('');

            $this->info('HTTP Status : '.$response->status());

            $this->line('');

            if ($response->successful()) {

                $this->info('Connection Successful.');

                $this->line('');

                $this->line(substr($response->body(), 0, 500));

            } else {

                $this->error('Connection Failed.');

                $this->line($response->body());

            }

        } catch (\Throwable $e) {

            $this->error($e->getMessage());

            return self::FAILURE;

        }

        return self::SUCCESS;
    }
}