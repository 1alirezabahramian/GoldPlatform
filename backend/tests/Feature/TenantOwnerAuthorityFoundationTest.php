<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantOwnerAuthority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOwnerAuthorityFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_has_no_implicit_owner_by_default(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertNull($tenant->owner_user_id);
        $this->assertFalse(TenantOwnerAuthority::allows($admin, $tenant));
    }

    public function test_explicit_owner_must_be_the_same_user_and_same_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $owner = User::factory()->create(['tenant_id' => $tenant->id]);
        $otherUser = User::factory()->create(['tenant_id' => $tenant->id]);

        $tenant->forceFill(['owner_user_id' => $owner->id])->save();
        $tenant->refresh();

        $this->assertTrue($tenant->owner->is($owner));
        $this->assertTrue(TenantOwnerAuthority::allows($owner, $tenant));
        $this->assertFalse(TenantOwnerAuthority::allows($otherUser, $tenant));
    }

    public function test_cross_tenant_owner_pointer_never_grants_authority(): void
    {
        $tenant = Tenant::factory()->create();
        $otherTenant = Tenant::factory()->create();
        $foreignUser = User::factory()->create(['tenant_id' => $otherTenant->id]);

        $tenant->forceFill(['owner_user_id' => $foreignUser->id])->save();
        $tenant->refresh();

        $this->assertFalse(TenantOwnerAuthority::allows($foreignUser, $tenant));
    }
}
