<?php

namespace Tests\Feature;

use App\Exceptions\BusinessException;
use App\Models\Account;
use App\Models\ExternalAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KimiaIdentityImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_established_user_account_binding_cannot_be_changed_or_removed(): void
    {
        $firstAccount = Account::create(['kimia_id' => 351]);
        $secondAccount = Account::create(['kimia_id' => 352]);
        $user = User::create(['mobile' => '09120000007']);

        $user->forceFill(['account_id' => $firstAccount->id])->save();

        foreach ([$secondAccount->id, null] as $replacement) {
            $linkedUser = $user->fresh();

            try {
                $linkedUser->forceFill(['account_id' => $replacement])->save();
                $this->fail('The established account binding was changed.');
            } catch (BusinessException) {
                $this->assertEquals($firstAccount->id, $user->fresh()->account_id);
            }
        }
    }

    #[Test]
    public function synchronized_kimia_identifiers_cannot_be_changed(): void
    {
        $account = Account::create(['kimia_id' => 353]);
        $externalAccount = ExternalAccount::create([
            'provider' => 'kimia',
            'external_id' => 353,
            'name' => 'حساب آزمایشی',
        ]);

        try {
            $account->update(['kimia_id' => 354]);
            $this->fail('The local Kimia identifier was changed.');
        } catch (BusinessException) {
            $this->assertEquals(353, $account->fresh()->kimia_id);
        }

        try {
            $externalAccount->update(['external_id' => 354]);
            $this->fail('The external Kimia identifier was changed.');
        } catch (BusinessException) {
            $this->assertEquals('353', (string) $externalAccount->fresh()->external_id);
        }

        try {
            $externalAccount->update(['provider' => 'other']);
            $this->fail('The external provider identity was changed.');
        } catch (BusinessException) {
            $this->assertSame('kimia', $externalAccount->fresh()->provider);
        }
    }
}
