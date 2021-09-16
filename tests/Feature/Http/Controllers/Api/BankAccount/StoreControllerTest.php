<?php

namespace Tests\Feature\Http\Controllers\Api\BankAccount;

use App\Enums\BankAccountEnum;
use App\Enums\PaymentEnum;
use App\Enums\TagsEnum;
use App\Enums\UserRoles;
use App\Models\ChargeCredit;
use App\Models\Company;
use App\Models\Credit;
use App\Models\Holiday;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\PaymentForm;
use App\Models\Store;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $guard_api = ['guard_name' => 'api'];
    protected $user = null;
    protected $personableType = null;
    protected $person = null;
    protected $userAdmin = null;
    protected $bankAccount = null;
    protected $starkbankStore = null;
    protected $payment = null;
    protected $credit = null;

    public function setUp (): void
    {
        parent::setUp();

        $role = UserRoles::STORE;

        $this->user = User::factory()->create();

        \Artisan::call('db:seed', ['-vvv' => true]);

        $this->user->assignRole($role);

        // Store
        $this->personableType = Store::class;
        $this->person         = Store::factory()
            ->hasBalance()
            ->hasWallets(Wallet::factory()->create())
            ->create([
                'user_id' => $this->user
            ]);

        $role = UserRoles::SUPER_ADMIN;

        $this->userAdmin = User::factory()->create();

        $this->userAdmin->assignRole($role);

        $this->starkbankStore = Store::factory()
            ->hasBalance()
            ->hasWallets(Wallet::factory()->create())
            ->create([
                'user_id' => $this->user
            ]);

        $this->bankAccount = BankAccount::factory()->create([
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->starkbankStore->id
        ]);

        $this->bankAccount->id = $this->starkbankId;
        $this->bankAccount->save();
        $this->bankAccount->refresh();

        $this->payment = Payment::factory()->create([
            'bank_account_id' => $this->starkbankId,
            'personable_type' => $this->personableType,
            'personable_id'   => $this->starkbankStore->id,
            'bank_account'    => $this->starkbankStore->bankAccounts,
            'requester_type'  => ((object)PaymentEnum::REQUESTER_TYPE)->manual,
            'status'          => ((object)PaymentEnum::STATUS)->pending,
            'scheduling_date' => Holiday::nextUtilDayForPayment()->format('Y-m-d'),
        ]);

        $this->payment->id = $this->starkbankId;
        $this->payment->save();
        $this->payment->refresh();

        $company = Company::factory()
            ->forUser()
            ->forSegment()
            ->hasAddress(1)
            ->hasWallets(Wallet::factory()->create())
            ->create();

        $paymentForm = PaymentForm::factory()->create();

        $this->credit = Credit::factory()
            ->create([
                'subtotal'                 => 10000,
                'balance_available_before' => 10000,
                'company_id'               => $company->id,
                'payment_form_id'          => $paymentForm->id,
                'wallet_id'                => $company->companyWallets->first()->wallet_id,
            ]);
    }

    private const DATA_STRUCTURE = [
        'id', 'bank_id', 'holder', 'document', 'account_type', 'agency', 'agency_digit',
        'account', 'account_digit', 'account', 'pix_key', 'operation',
        'bank' => ['id', 'name'],
    ];

    private function deposit ($status)
    {
        return [
            "event" => [
                "created"      => "2021-06-08T14 =>31 =>24.848733+00 =>00",
                "id"           => "5073975092707328",
                "log"          => [
                    "created" => "2021-06-08T14 =>31 =>24.076913+00 =>00",
                    "deposit" => [
                        "accountNumber"  => "333300",
                        "accountType"    => "checking",
                        "amount"         => 1,
                        "bankCode"       => "60701190",
                        "branchCode"     => "6082",
                        "created"        => "2021-06-08T14 =>31 =>23.616671+00 =>00",
                        "fee"            => 0,
                        "id"             => "5472977084743680",
                        "name"           => "MANUEL FERREIRA DA SILVA FILHO",
                        "status"         => $status,
                        "tags"           => [
                            "credit" . $this->credit->id,
                        ],
                        "taxId"          => "017.132.405-64",
                        "transactionIds" => [
                            "6500568029724672"
                        ],
                        "type"           => "pix",
                        "updated"        => "2021-06-08T14 =>31 =>24.076960+00 =>00"
                    ],
                    "errors"  => [],
                    "id"      => "6405937799626752",
                    "type"    => "credited"
                ],
                "subscription" => "deposit",
                "workspaceId"  => "6122930379423744"
            ]
        ];
    }

    /**
     * @test
     */
    public function non_authenticated_users_cannot_access_the_following_endpoints_for_the_bank_accounts_api ()
    {
        $index = $this->json('GET', '/api/bank_accounts');
        $index->assertStatus(401);

        $store = $this->json('POST', '/api/bank_accounts');
        $store->assertStatus(401);

        $show = $this->json('GET', '/api/bank_accounts/-1');
        $show->assertStatus(401);

        $update = $this->json('PUT', '/api/bank_accounts/-1');
        $update->assertStatus(401);

        $patch = $this->json('PATCH', "/api/bank_accounts/-1/set_main");
        $patch->assertStatus(401);

        $delete = $this->json('DELETE', '/api/bank_accounts/-1');
        $delete->assertStatus(401);

        $restore = $this->json('PATCH', '/api/bank_accounts/-1/restore');
        $restore->assertStatus(401);

        $audits = $this->json('GET', '/api/bank_accounts/-1/audits');
        $audits->assertStatus(401);
    }

    /**
     * @test
     */
    public function non_permission_users_cannot_access_the_following_endpoints_for_the_bank_accounts_api ()
    {
        $user = User::factory()->create();

        Passport::actingAs($user);

        $index = $this->json('GET', '/api/bank_accounts');
        $index->assertStatus(403);

        $store = $this->json('POST', '/api/bank_accounts');
        $store->assertStatus(403);

        $show = $this->json('GET', '/api/bank_accounts/-1');
        $show->assertStatus(403);

        $update = $this->json('PUT', '/api/bank_accounts/-1');
        $update->assertStatus(403);

        $delete = $this->json('DELETE', '/api/bank_accounts/-1');
        $delete->assertStatus(403);

        $restore = $this->json('PATCH', '/api/bank_accounts/-1/restore');
        $restore->assertStatus(403);

        $audits = $this->json('GET', '/api/bank_accounts/-1/audits');
        $audits->assertStatus(403);
    }

    /**
     * @test
     */
    public function will_return_404_if_not_found ()
    {
        Passport::actingAs($this->user);

        $show = $this->json('GET', '/api/bank_accounts/-1');
        $show->assertStatus(404);

        $update = $this->json('PUT', '/api/bank_accounts/-1');
        $update->assertStatus(404);

        $delete = $this->json('DELETE', '/api/bank_accounts/-1');
        $delete->assertStatus(403);

        $restore = $this->json('PATCH', '/api/bank_accounts/-1/restore');
        $restore->assertStatus(403);

        $audits = $this->json('GET', '/api/bank_accounts/-1/audits');
        $audits->assertStatus(403);
    }

    /**
     * @test
     */
    public function can_create_bank_accounts ()
    {
        $bank_account = BankAccount::factory()->make([
            "document" => $this->person->document,
            "holder"   => $this->person->legal_name,
        ]);

        Passport::actingAs($this->user);
        $response = $this->json('POST', "/api/bank_accounts", $bank_account->toArray());
        $response->assertStatus(201)
            ->assertJsonStructure(self::DATA_STRUCTURE)
            ->assertJson($bank_account->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at'])->toArray());
        $this->assertDatabaseHas("bank_accounts", [
            'bank_id'       => $bank_account->bank_id,
            'holder'        => $bank_account->holder,
            'document'      => $bank_account->document,
            'account_type'  => $bank_account->account_type,
            'agency'        => $bank_account->agency,
            'agency_digit'  => $bank_account->agency_digit,
            'account_digit' => $bank_account->account_digit,
            'pix_key'       => $bank_account->pix_key,
            'operation'     => $bank_account->operation,
        ]);

        //TEST DUPLICATE
        $response = $this->json('POST', "/api/bank_accounts", $bank_account->toArray());
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function will_return_422_if_validation_fail ()
    {
        $bank_account = BankAccount::factory()->make();
        $fakeData     = BankAccount::factory()->make();

        Passport::actingAs($this->user);

        unset($bank_account->account_digit);

        $response = $this->json('POST', "/api/bank_accounts", $bank_account->toArray());

        $response->assertStatus(422)
            ->assertJson([
                'error'   => true,
                'message' => [
                    ['account_digit' => ['validation.required']]
                ]
            ]);

        //TEST SUCCESSFUL
        $bank_account = BankAccount::factory()->make();

        BankAccount::factory()->create([
            "document"              => $this->person->document,
            "holder"                => $this->person->legal_name,
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id
        ]);

        $bank_account["id"]      = $this->person->bankAccounts[0]->id;
        $bank_account["account"] = $this->person->bankAccounts[0]->account;

        Passport::actingAs($this->user);

        unset($fakeData->account);

        $response = $this->json('PUT', "/api/bank_accounts/" . $bank_account["id"], $fakeData->toArray());

        $response->assertStatus(422)
            ->assertJson([
                'error'   => true,
                'message' => [
                    ['account' => ['validation.required']]
                ]
            ]);


        $response = $this->json('PUT', "/api/bank_accounts/" . $bank_account["id"], $bank_account->toArray());

        $response->assertStatus(200)
            ->assertJsonStructure(self::DATA_STRUCTURE)
            ->assertJson($bank_account->with("bank")->find($bank_account['id'])->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at'])->toArray());

        $this->assertDatabaseHas("bank_accounts", [
            'id'            => $bank_account->id,
            'bank_id'       => $bank_account->bank_id,
            'holder'        => $this->person->legal_name,
            'document'      => $this->person->document,
            'account_type'  => $bank_account->account_type,
            'agency'        => $bank_account->agency,
            'agency_digit'  => $bank_account->agency_digit,
            'account_digit' => $bank_account->account_digit,
            'pix_key'       => $bank_account->pix_key,
            'operation'     => $bank_account->operation,
        ]);

        //TEST MAXIMUM UPDATES
        $response = $this->json('PUT', "/api/bank_accounts/" . $bank_account["id"], $bank_account->toArray());
        $response->assertStatus(200);

        $response = $this->json('PUT', "/api/bank_accounts/" . $bank_account["id"], $bank_account->toArray());
        $response->assertStatus(200);

        $response = $this->json('PUT', "/api/bank_accounts/" . $bank_account["id"], $bank_account->toArray());
        $response->assertStatus(422)
            ->assertJson([
                'error'   => true,
                'message' => [
                    ['error' => ['maximum number of updates exceeded']]
                ]
            ]);
    }

    /**
     * @test
     */
    public function can_update_bank_accounts ()
    {
        //TEST SUCCESSFUL
        $bank_account = BankAccount::factory()->make();

        BankAccount::factory()->create([
            "document"              => $this->person->document,
            "holder"                => $this->person->legal_name,
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id
        ]);

        $bank_account["id"] = $this->person->bankAccounts[0]->id;

        Passport::actingAs($this->user);

        $response = $this->json('PUT', "/api/bank_accounts/" . $bank_account["id"], $bank_account->toArray());

        $response->assertStatus(200)
            ->assertJsonStructure(self::DATA_STRUCTURE)
            ->assertJson($bank_account->with("bank")->find($bank_account['id'])->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at'])->toArray());

        $this->assertDatabaseHas("bank_accounts", [
            'id'            => $bank_account->id,
            'bank_id'       => $bank_account->bank_id,
            'holder'        => $this->person->legal_name,
            'document'      => $this->person->document,
            'account_type'  => $bank_account->account_type,
            'agency'        => $bank_account->agency,
            'agency_digit'  => $bank_account->agency_digit,
            'account_digit' => $bank_account->account_digit,
            'pix_key'       => $bank_account->pix_key,
            'operation'     => $bank_account->operation,
        ]);
    }

    /**
     * @test
     */
    public function can_return_a_bank_account ()
    {
        BankAccount::factory()->create([
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id
        ]);
        $bank_account = $this->person->bankAccounts->load("bank")->first();

        Passport::actingAs($this->user);

        $response = $this->json('GET', "/api/bank_accounts/" . $bank_account->id);

        $response->assertStatus(200)
            ->assertJsonStructure(self::DATA_STRUCTURE)
            ->assertJson($bank_account->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at'])->toArray());
    }

    /**
     * @test
     */
    public function can_delete_a_store ()
    {
        BankAccount::factory()->create([
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id
        ]);
        $bank_account = $this->person->bankAccounts->first();

        $user = User::factory()->create();
        $user->assignRole(UserRoles::SUPER_ADMIN);
        Passport::actingAs($user);

        $response = $this->json('DELETE', "/api/bank_accounts/" . $bank_account->id);

        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertSoftDeleted('bank_accounts', [
            'id' => $bank_account->id,
        ]);
    }

    /**
     * @test
     */
    public function can_restore_a_bank_account ()
    {
        BankAccount::factory()->create([
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id
        ]);
        $bank_account = $this->person->bankAccounts->first();

        $bank_account->delete();

        $user = User::factory()->create();
        $user->assignRole(UserRoles::SUPER_ADMIN);
        Passport::actingAs($user);

        $response = $this->json('PATCH', "/api/bank_accounts/" . $bank_account->id . "/restore");

        $response->assertStatus(200)
            ->assertJsonStructure(self::DATA_STRUCTURE)
            ->assertJson($bank_account->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at'])->toArray());


        $this->assertDatabaseHas('bank_accounts', [
            'id'         => $bank_account->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * @test
     */
    public function can_get_audits_for_bank_account ()
    {
        BankAccount::factory()->create([
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id
        ]);
        $bank_account = $this->person->bankAccounts->first();

        $user = User::factory()->create();
        $user->assignRole(UserRoles::SUPER_ADMIN);

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/bank_accounts/" . $bank_account->id . "/audits");
        $audits   = $response->getData();


        $response->assertStatus(200)
            ->assertJson([
                0 => [
                    'id'         => $audits[0]->id,
                    'event'      => 'created',
                    'ip_address' => '127.0.0.1',
                    'old_values' => [],
                    'new_values' => [
                        'holder'                => $bank_account->holder,
                        'document'              => $bank_account->document,
                        'account_type'          => $bank_account->account_type,
                        'agency'                => $bank_account->agency,
                        'agency_digit'          => $bank_account->agency_digit,
                        'account'               => $bank_account->account,
                        'account_digit'         => $bank_account->account_digit,
                        'pix_key'               => $bank_account->pix_key,
                        'operation'             => $bank_account->operation,
                        'bank_id'               => $bank_account->bank_id,
                        'bank_accountable_type' => Store::class,
                        'bank_accountable_id'   => $bank_account->bank_accountable_id,
                        'id'                    => $bank_account->id,
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function can_set_main_bank_accounts ()
    {
        $bank_account1 = BankAccount::factory()->create([
            "document"              => $this->person->document,
            "holder"                => $this->person->legal_name,
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id,
            "verified_at"           => Carbon::now()
        ]);

        $bank_account2 = BankAccount::factory()->create([
            "document"              => $this->person->document,
            "holder"                => $this->person->legal_name,
            "bank_accountable_type" => $this->personableType,
            "bank_accountable_id"   => $this->person->id,
            "verified_at"           => Carbon::now()
        ]);


        Passport::actingAs($this->user);

        $response = $this->json('PATCH', "/api/bank_accounts/" . $bank_account1->id . '/set_main');


        $response->assertStatus(200)
            ->assertJsonStructure(self::DATA_STRUCTURE)
            ->assertJson($bank_account1->with("bank")->find($bank_account1['id'])->makeHidden(['status', 'verifications', 'verified_at', 'deleted_at'])->toArray());

        $this->assertDatabaseHas("bank_accounts", [
            'id'            => $bank_account1->id,
            'bank_id'       => $bank_account1->bank_id,
            'holder'        => $this->person->legal_name,
            'document'      => $this->person->document,
            'account_type'  => $bank_account1->account_type,
            'agency'        => $bank_account1->agency,
            'agency_digit'  => $bank_account1->agency_digit,
            'account_digit' => $bank_account1->account_digit,
            'pix_key'       => $bank_account1->pix_key,
            'operation'     => $bank_account1->operation,
            'main_account'  => 1,
        ]);

        $this->assertDatabaseHas("bank_accounts", [
            'id'            => $bank_account2->id,
            'bank_id'       => $bank_account2->bank_id,
            'holder'        => $this->person->legal_name,
            'document'      => $this->person->document,
            'account_type'  => $bank_account2->account_type,
            'agency'        => $bank_account2->agency,
            'agency_digit'  => $bank_account2->agency_digit,
            'account_digit' => $bank_account2->account_digit,
            'pix_key'       => $bank_account2->pix_key,
            'operation'     => $bank_account2->operation,
            'main_account'  => 0,
        ]);
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_check_bank_account_and_status_success ()
    {
        Passport::actingAs($this->userAdmin);

        $data = $this->transfer('success', ["check_bank_account"]);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();

        self::assertEquals(BankAccountEnum::STATUS['confirmed'], $this->bankAccount->refresh()->status);

        self::assertEquals(true, $this->bankAccount->refresh()->verified_at != null);

        self::assertEquals(null, $this->bankAccount->refresh()->info);
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_check_bank_account_and_status_processing ()
    {
        Passport::actingAs($this->userAdmin);

        $data = $this->transfer('processing', ["check_bank_account"]);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();

        self::assertEquals(BankAccountEnum::STATUS['processing'], $this->bankAccount->refresh()->status);

        self::assertEquals(null, $this->bankAccount->refresh()->info);
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_check_bank_account_and_status_duplicated_transfer ()
    {
        Passport::actingAs($this->userAdmin);

        $data = $this->transfer('failed', ["check_bank_account"], ["Duplicated transfer"]);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_check_bank_account_and_status_invalid_account ()
    {
        Passport::actingAs($this->userAdmin);

        $data = $this->transfer('failed', ["check_bank_account"], ["Invalid account number"]);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();

        self::assertEquals(BankAccountEnum::STATUS['invalid'], $this->bankAccount->refresh()->status);

        self::assertEquals(["message" => "Invalid account number"], $this->bankAccount->refresh()->info);
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_payment_transfer_and_status_processing ()
    {
        Passport::actingAs($this->userAdmin);

        $data = $this->transfer('processing', ["transfer_payment"]);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();

        $this->assertDatabaseHas('payment_histories', [
            'payment_id'  => $this->starkbankId,
            'description' => 'Atualização: Pagamento em processamento.',
            'status'      => ((object)PaymentEnum::STATUS)->processing,
        ]);
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_payment_transfer_and_status_failed ()
    {
        Passport::actingAs($this->userAdmin);

        $error = ['generic error'];

        $data = $this->transfer('failed', ["transfer_payment"], $error);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();

        $this->assertDatabaseHas('payment_histories', [
            'payment_id'  => $this->starkbankId,
            'description' => $error,
            'status'      => ((object)PaymentEnum::STATUS)->divergent_info,
        ]);

        $this->assertDatabaseHas('payments', [
            'id'     => $this->payment->id,
            'status' => ((object)PaymentEnum::STATUS)->divergent_info,
        ]);
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_payment_transfer_and_status_canceled ()
    {
        Passport::actingAs($this->userAdmin);

        $error = ['generic error'];

        $data = $this->transfer('canceled', ["transfer_payment"], $error);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();

        $this->assertDatabaseHas('payment_histories', [
            'payment_id'  => $this->starkbankId,
            'description' => 'Pagamento cancelado',
            'status'      => ((object)PaymentEnum::STATUS)->divergent_info,
        ]);

        $this->assertDatabaseHas('payments', [
            'id'     => $this->payment->id,
            'status' => PaymentEnum::STATUS['divergent_info'],
        ]);
    }

    /**
     * @test
     */
    public function can_transfer_with_tag_payment_transfer_and_status_success ()
    {
        Passport::actingAs($this->userAdmin);

        $data = $this->transfer('success', ["transfer_payment"]);

        $response = $this->json('POST', '/api/webhook/starkbank/transfer', $data);

        $response->assertOk();

        $this->assertDatabaseHas('payment_histories', [
            'payment_id'  => $this->starkbankId,
            'description' => 'Pagamento finalizado',
            'status'      => PaymentEnum::STATUS['finalized'],
        ]);

        $userName = $this->userAdmin->name;

        $this->assertDatabaseHas('payment_histories', [
            'payment_id'  => $this->starkbankId,
            'description' => "Pagamento realizado. ($userName)",
            'status'      => PaymentEnum::STATUS['paid'],
        ]);

        $this->assertDatabaseHas('payments', [
            'id'     => $this->payment->id,
            'status' => PaymentEnum::STATUS['finalized'],
        ]);

        $tax_amount = $this->payment->bank_rate_amount + $this->payment->tax_rate_amount;

        $payment_amount = $this->payment->amount + $tax_amount;

        $this->assertDatabaseHas('store_balances', [
            'id'      => $this->starkbankStore->balance->id,
            'blocked' => $payment_amount * -1,
        ]);
    }

    /**
     * @test
     */
    public function can_pix_cash_in ()
    {
        Passport::actingAs($this->userAdmin);

        $data = $this->deposit('created');

        $chargeCredit = ChargeCredit::factory()->create([
            'reference'       => $data['event']['log']['deposit']['tags'][0],
            'credit_id'       => $this->credit->id,
            'payment_form_id' => $this->credit->payment_form_id,
        ]);

        $this->credit->payment_value = $data['event']['log']['deposit']['amount'];
        $this->credit->save();
        $this->credit->refresh();

        $response = $this->json('POST', '/api/webhook/starkbank/pix-cash-in', $data);

        $response->assertOk();

        $this->assertDatabaseHas('charge_credits', [
            'id'     => $chargeCredit->id,
            'status' => ChargeCredit::status[2],
        ]);

        $companyWallet = $this->credit->company->companyWallets()
            ->where('wallet_id', $this->credit->wallet_id)
            ->first();

        $this->assertDatabaseHas('company_wallet', [
            'id'              => $companyWallet->id,
            'blocked_balance' => $this->credit->subtotal * -1,
        ]);

        $this->credit->refresh();
        $this->credit->status = Credit::status[0];
        $this->credit->save();
        $this->credit->refresh();

        $companyWallet->blocked_balance = 0;
        $companyWallet->save();
        $companyWallet->refresh();

        $chargeCredit->refresh();
        $chargeCredit->status    = ChargeCredit::status[0];
        $chargeCredit->reference = $data['event']['log']['deposit']['tags'][0];
        $chargeCredit->save();
        $chargeCredit->refresh();

        $response = $this->json('POST', '/api/webhook/starkbank/pix-cash-in', $data);

        $response->assertOk();

        $this->assertDatabaseHas('charge_credits', [
            'id'     => $chargeCredit->id,
            'status' => ChargeCredit::status[2],
        ]);

        $companyWallet = $this->credit->company->companyWallets()
            ->where('wallet_id', $this->credit->wallet_id)
            ->first();

        $this->assertDatabaseHas('company_wallet', [
            'id'              => $companyWallet->id,
            'blocked_balance' => $this->credit->balance_available_before * -1,
        ]);
    }
}
