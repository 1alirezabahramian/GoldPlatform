<?php

namespace Tests\Feature;

use App\Models\ExternalAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KimiaInspectSyncStateCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_verifies_an_account_without_printing_identity_fields(): void
    {
        ExternalAccount::create([
            'provider' => 'kimia',
            'external_id' => 350,
            'name' => 'Sensitive Customer Name',
            'mobile' => '09120000000',
            'national_id' => '0012345678',
            'type' => 3,
            'is_active' => true,
            'sync_status' => 'synced',
            'last_synced_at' => now(),
        ]);

        $this->artisan('kimia:inspect-sync-state', ['--account' => 350])
            ->expectsOutputToContain('350')
            ->expectsOutputToContain('Customer name, mobile, national code, and raw payload are omitted.')
            ->doesntExpectOutputToContain('Sensitive Customer Name')
            ->doesntExpectOutputToContain('09120000000')
            ->doesntExpectOutputToContain('0012345678')
            ->assertSuccessful();
    }

    #[Test]
    public function it_fails_when_the_requested_account_is_not_synchronized(): void
    {
        $this->artisan('kimia:inspect-sync-state', ['--account' => 999])
            ->expectsOutputToContain('missing')
            ->assertFailed();
    }
}
