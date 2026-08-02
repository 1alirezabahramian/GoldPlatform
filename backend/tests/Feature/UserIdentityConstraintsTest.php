<?php

namespace Tests\Feature;

use App\Exceptions\BusinessException;
use App\Http\Requests\RegisterRequest;
use App\Models\Account;
use App\Models\ExternalAccount;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserIdentityConstraintsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function two_accounts_may_share_a_national_code_but_not_a_mobile_number(): void
    {
        User::create([
            'mobile' => '09120000001',
            'national_code' => '0012345678',
        ]);

        $validSecondAccount = Validator::make([
            'mobile' => '09120000002',
            'national_code' => '0012345678',
            'password' => 'secret12',
        ], (new RegisterRequest())->rules());

        $this->assertFalse($validSecondAccount->fails());

        $duplicateMobile = Validator::make([
            'mobile' => '09120000001',
            'national_code' => '0098765432',
            'password' => 'secret12',
        ], (new RegisterRequest())->rules());

        $this->assertTrue($duplicateMobile->errors()->has('mobile'));

        $second = User::create([
            'mobile' => '09120000002',
            'national_code' => '0012345678',
        ]);

        $this->assertSame('0012345678', $second->national_code);
    }

    #[Test]
    public function mobile_and_national_code_remain_editable(): void
    {
        $user = User::create([
            'mobile' => '09120000003',
            'national_code' => '0012345678',
        ]);

        $user->update([
            'mobile' => '09120000004',
            'national_code' => '0098765432',
        ]);

        $this->assertSame('09120000004', $user->fresh()->mobile);
        $this->assertSame('0098765432', $user->fresh()->national_code);
    }

    #[Test]
    public function one_local_account_cannot_be_linked_to_two_users(): void
    {
        $account = Account::create(['kimia_id' => 350]);
        $first = User::create(['mobile' => '09120000005']);
        $second = User::create(['mobile' => '09120000006']);

        $first->forceFill(['account_id' => $account->id])->save();

        $this->expectException(QueryException::class);

        $second->forceFill(['account_id' => $account->id])->save();
    }

    #[Test]
    public function an_established_user_account_binding_cannot_be_changed(): void
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
                $this->assertEquals(
                    $firstAccount->id,
                    $user->fresh()->account_id
                );
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
            $this->assertEquals(353, $externalAccount->fresh()->external_id);
        }
    }
}
