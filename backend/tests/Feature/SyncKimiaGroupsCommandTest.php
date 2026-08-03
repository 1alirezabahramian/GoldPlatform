<?php

namespace Tests\Feature;

use App\Integrations\Kimia\Repositories\KimiaAccountRepository;
use App\Models\AccountGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SyncKimiaGroupsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_updates_kimia_groups_without_duplicates(): void
    {
        $repository = Mockery::mock(KimiaAccountRepository::class);

        $repository->shouldReceive('groups')
            ->with(1)
            ->twice()
            ->andReturn(
                [[
                    'Id' => 101,
                    'Name' => 'Old Group Name',
                    'AccountType' => 1,
                ]],
                [[
                    'Id' => 101,
                    'Name' => 'Updated Group Name',
                    'AccountType' => 1,
                ]]
            );

        foreach ([3, 5, 6, 8, 9, 10, 11, 12] as $type) {
            $repository->shouldReceive('groups')
                ->with($type)
                ->twice()
                ->andReturn([]);
        }

        $this->app->instance(KimiaAccountRepository::class, $repository);

        $this->artisan('kimia:sync-groups')
            ->assertSuccessful();

        $this->assertDatabaseHas('account_groups', [
            'kimia_id' => 101,
            'account_type' => 1,
            'name' => 'Old Group Name',
            'is_active' => true,
        ]);

        $this->artisan('kimia:sync-groups')
            ->assertSuccessful();

        $this->assertDatabaseCount('account_groups', 1);

        $this->assertDatabaseHas('account_groups', [
            'kimia_id' => 101,
            'account_type' => 1,
            'name' => 'Updated Group Name',
            'is_active' => true,
        ]);
    }

    public function test_it_skips_unchanged_kimia_groups(): void
    {
        AccountGroup::create([
            'kimia_id' => 202,
            'account_type' => 3,
            'name' => 'Unchanged Group',
            'is_active' => true,
            'synced_at' => now()->subDay(),
        ]);

        $group = AccountGroup::query()
            ->where('kimia_id', 202)
            ->firstOrFail();

        $originalUpdatedAt = $group->updated_at;
        $originalSyncedAt = $group->synced_at;

        $repository = Mockery::mock(KimiaAccountRepository::class);

        foreach ([1, 5, 6, 8, 9, 10, 11, 12] as $type) {
            $repository->shouldReceive('groups')
                ->with($type)
                ->once()
                ->andReturn([]);
        }

        $repository->shouldReceive('groups')
            ->with(3)
            ->once()
            ->andReturn([[
                'Id' => 202,
                'Name' => 'Unchanged Group',
                'AccountType' => 3,
            ]]);

        $this->app->instance(KimiaAccountRepository::class, $repository);

        $this->artisan('kimia:sync-groups')
            ->assertSuccessful();

        $group->refresh();

        $this->assertDatabaseCount('account_groups', 1);
        $this->assertTrue($group->updated_at->equalTo($originalUpdatedAt));
        $this->assertTrue($group->synced_at->equalTo($originalSyncedAt));
    }
}
