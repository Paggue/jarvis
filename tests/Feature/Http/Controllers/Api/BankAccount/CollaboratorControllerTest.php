<?php

namespace Tests\Feature\Http\Controllers\Api\BankAccount;

use App\Enums\UserRoles;
use App\Models\Company;
use App\Models\Group;
use App\Models\BankAccount;
use App\Models\Collaborator;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;
use function Clue\StreamFilter\fun;

class CollaboratorControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $user = null;
    protected $personableType = null;
    protected $person = null;
    protected $guard_api = ['guard_name' => 'api'];

    public function setUp (): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        \Artisan::call('db:seed', ['-vvv' => true]);

        $this->user->assignRole(UserRoles::COLLABORATOR);

        $company = Company::factory()
            ->forUser()
            ->forSegment()
            ->create();

        $group = Group::factory()->create([
            "company_id" => $company->id,
        ]);

        // Collaborator
        $this->personableType = Collaborator::class;
        $this->person         = Collaborator::factory()
            ->hasBalance()
            ->hasWallets(Wallet::factory()->create())
            ->create([
                "user_id"    => $this->user,
                "document"   => $this->user->document,
                "company_id" => $company->id,
                "group_id"   => $group->id,
            ]);
    }

    private const DATA_STRUCTURE = [
        'id', 'bank_id', 'holder', 'document', 'account_type', 'agency', 'agency_digit',
        'account', 'account_digit', 'account', 'pix_key', 'operation',
        'bank' => ['id', 'name'],
    ];


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
            ->assertJson($bank_account->with("bank")->first()->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at'])->toArray());

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
            ->assertJson($bank_account->with("bank")->first()->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at'])->toArray());

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
    public function can_delete_a_bank_account ()
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
            ->assertJson($bank_account->makeHidden(['main_account', 'status', 'verifications', 'verified_at', 'deleted_at', 'created_at', 'updated_at'])->toArray());


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
                        'bank_accountable_type' => Collaborator::class,
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
}
