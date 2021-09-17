<?php

namespace Lara\Jarvis\Tests;

use Dotenv\Dotenv;
use Lara\Jarvis\Enums\TagsEnum;
use Lara\Jarvis\Providers\JarvisServiceProvider;
use Laravel\Passport\PassportServiceProvider;

include __DIR__ . '/../config/config.php';

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $loadEnvironmentVariables = true;
    protected $starkbankId = null;

    public function setUp (): void
    {
        parent::setUp();

        $this->withHeaders(['Accept' => 'application/json']);

        $this->starkbankId = 27;
    }

    protected function getPackageProviders ($app)
    {
        return [
            JarvisServiceProvider::class,
            PassportServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp ($app)
    {
        // perform environment setup
        include_once __DIR__ . '/../database/migrations/create_users_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_states_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_cities_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_comments_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_banks_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_settings_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_holidays_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_audits_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_addresses_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_user_device_tokens_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_bank_accounts_table.php.stub';


        (new \CreateUsersTable)->up();
        (new \CreateStatesTable)->up();
        (new \CreateCitiesTable)->up();
        (new \CreateCommentsTable)->up();
        (new \CreateBanksTable)->up();
        (new \CreateSettingsTable)->up();
        (new \CreateHolidaysTable)->up();
        (new \CreateAuditsTable)->up();
        (new \CreateAddressesTable)->up();
        (new \CreateUserDeviceTokensTable)->up();
        (new \CreateBankAccountsTable)->up();

        $app['config']->set('auth.providers.users.model', Lara\Jarvis\Tests\User::class);
        $app['config']->set('auth.guards.api.driver', 'passport');
        $app['config']->set('auth.guards.api.provider', 'users');

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../', '.env.testing');
        $dotenv->load();

        $app['config']->set('jarvis', [
            'routes' => [
                'api_prefix'  => 'api',
                'auth_prefix' => 'api/auth',
                'middleware'  => ['api'],
            ],

            'app' => [
                'name'     => env('APP_NAME'),
                'url'      => env('APP_URL'),
                'url_site' => env('APP_URL_SITE')
            ],

            'trello' => [
                'key' => env('TRELLO_KEY'),

                'token' => env('TRELLO_TOKEN'),

                'production' => env('TRELLO_PRODUCTION', false),
            ],

            'pipefy' => [
                'user_token' => env('PIPEFY_USER_TOKEN'),

                'token' => env('PIPEFY_TOKEN'),

                'production' => env('PIPEFY_PRODUCTION', false),
            ],

            'comtele' => [
                'url' => env('COMTELE_URL'),

                'token' => env('COMTELE_TOKEN'),

                'production' => env('COMTELE_PRODUCTION', false),
            ],

            'starkbank' => [
                'project_id'  => env('STARKBANK_PROJECT_ID'),
                'environment' => env('STARKBANK_ENVIRONMENT'),
                'token'       => env('STARKBANK_TOKEN'),
            ],

            'pagseguro' => [
                'base_url'         => env('PAGSEGURO_URL'),
                'token'            => env('PAGSEGURO_TOKEN'),
                'email'            => env('PAGSEGURO_EMAIL'),
                'webhook_base_url' => env('PAGSEGURO_WEBHOOK_BASE_URL'),
            ],

            'pix' => [
                'pix_key'       => env('PIX_KEY'),
                'merchant_name' => env('PIX_MERCHANT_NAME'),
                'merchant_city' => env('PIX_MERCHANT_CITY'),
            ],

            'metabase_secret_key' => env('METABASE_SECRET_KEY'),

            'maps_api_key' => env('MAPS_API_KEY'),

            'cnpja_api_key' => env('CNPJA_API_KEY'),

            'firebase' => [
                'token' => env('FIREBASE_TOKEN'),
            ],

            's3' => [
                'driver'   => 's3',
                'key'      => env('AWS_ACCESS_KEY_ID'),
                'secret'   => env('AWS_SECRET_ACCESS_KEY'),
                'region'   => env('AWS_REGION'),
                'bucket'   => env('AWS_BUCKET'),
            ],
        ]);

        $array = include $config;

    }

    /**
     * Ignore package discovery from.
     *
     * @return array
     */
    public function ignorePackageDiscoveriesFrom ()
    {
        return [];
    }

    protected function loadEnvVariables ()
    {

    }

    public function transfer ($status, $tag, $errors = [])
    {
        switch (strtoupper($tag[0])) {
            case TagsEnum::CHECK_BANK_ACCOUNT:
                $externalId = $this->starkbankId . "UUIDc2f39d5b-82a3-496d-8845-b5e33d15060b";
                break;
            case TagsEnum::TRANSFER_PAYMENT:
                $externalId = TagsEnum::TRANSFER_PAYMENT . $this->starkbankId;
                break;
            default:
                $externalId = null;
        }

        return [
            "event" => [
                "created"      => "2021-06-14T16:28:58.721366+00:00",
                "id"           => "5633272612651008",
                "log"          => [
                    "created"  => "2021-06-14T16:28:58.171895+00:00",
                    "errors"   => $errors,
                    "id"       => "5550417949753344",
                    "transfer" => [
                        "accountNumber"  => "60502-5",
                        "accountType"    => "checking",
                        "amount"         => 1,
                        "bankCode"       => "60746948",
                        "branchCode"     => "3516",
                        "created"        => "2021-06-14T16:28:52.809526+00:00",
                        "description"    => "COMEB COMERCIAL DE ESTIVAS BARRETO LTDA (16.149.098/0001-83)",
                        "externalId"     => $externalId,
                        "fee"            => 50,
                        "id"             => "5405882670120960",
                        "name"           => "COMEB COMERCIAL DE ESTIVAS BARRETO LTDA",
                        "scheduled"      => "2021-06-14T16:28:52.780489+00:00",
                        "status"         => $status,
                        "tags"           => $tag,
                        "taxId"          => "16.149.098/0001-83",
                        "transactionIds" => [
                            "5680936335179776"
                        ],
                        "updated"        => "2021-06-14T16:28:58.171958+00:00"
                    ],
                    "type"     => "failed"
                ],
                "subscription" => "transfer",
                "workspaceId"  => "6122930379423744"
            ]
        ];
    }

    const BASE64_EXAMPLE = "data:@file/jpeg;base64,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";

    const BASE64_PDF = "JVBERi0xLjMNCiXi48/TDQoNCjEgMCBvYmoNCjw8DQovVHlwZSAvQ2F0YWxvZw0KL091dGxpbmVzIDIgMCBSDQovUGFnZXMgMyAwIFINCj4+DQplbmRvYmoNCg0KMiAwIG9iag0KPDwNCi9UeXBlIC9PdXRsaW5lcw0KL0NvdW50IDANCj4+DQplbmRvYmoNCg0KMyAwIG9iag0KPDwNCi9UeXBlIC9QYWdlcw0KL0NvdW50IDINCi9LaWRzIFsgNCAwIFIgNiAwIFIgXSANCj4+DQplbmRvYmoNCg0KNCAwIG9iag0KPDwNCi9UeXBlIC9QYWdlDQovUGFyZW50IDMgMCBSDQovUmVzb3VyY2VzIDw8DQovRm9udCA8PA0KL0YxIDkgMCBSIA0KPj4NCi9Qcm9jU2V0IDggMCBSDQo+Pg0KL01lZGlhQm94IFswIDAgNjEyLjAwMDAgNzkyLjAwMDBdDQovQ29udGVudHMgNSAwIFINCj4+DQplbmRvYmoNCg0KNSAwIG9iag0KPDwgL0xlbmd0aCAxMDc0ID4+DQpzdHJlYW0NCjIgSg0KQlQNCjAgMCAwIHJnDQovRjEgMDAyNyBUZg0KNTcuMzc1MCA3MjIuMjgwMCBUZA0KKCBBIFNpbXBsZSBQREYgRmlsZSApIFRqDQpFVA0KQlQNCi9GMSAwMDEwIFRmDQo2OS4yNTAwIDY4OC42MDgwIFRkDQooIFRoaXMgaXMgYSBzbWFsbCBkZW1vbnN0cmF0aW9uIC5wZGYgZmlsZSAtICkgVGoNCkVUDQpCVA0KL0YxIDAwMTAgVGYNCjY5LjI1MDAgNjY0LjcwNDAgVGQNCigganVzdCBmb3IgdXNlIGluIHRoZSBWaXJ0dWFsIE1lY2hhbmljcyB0dXRvcmlhbHMuIE1vcmUgdGV4dC4gQW5kIG1vcmUgKSBUag0KRVQNCkJUDQovRjEgMDAxMCBUZg0KNjkuMjUwMCA2NTIuNzUyMCBUZA0KKCB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiApIFRqDQpFVA0KQlQNCi9GMSAwMDEwIFRmDQo2OS4yNTAwIDYyOC44NDgwIFRkDQooIEFuZCBtb3JlIHRleHQuIEFuZCBtb3JlIHRleHQuIEFuZCBtb3JlIHRleHQuIEFuZCBtb3JlIHRleHQuIEFuZCBtb3JlICkgVGoNCkVUDQpCVA0KL0YxIDAwMTAgVGYNCjY5LjI1MDAgNjE2Ljg5NjAgVGQNCiggdGV4dC4gQW5kIG1vcmUgdGV4dC4gQm9yaW5nLCB6enp6ei4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gQW5kICkgVGoNCkVUDQpCVA0KL0YxIDAwMTAgVGYNCjY5LjI1MDAgNjA0Ljk0NDAgVGQNCiggbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiApIFRqDQpFVA0KQlQNCi9GMSAwMDEwIFRmDQo2OS4yNTAwIDU5Mi45OTIwIFRkDQooIEFuZCBtb3JlIHRleHQuIEFuZCBtb3JlIHRleHQuICkgVGoNCkVUDQpCVA0KL0YxIDAwMTAgVGYNCjY5LjI1MDAgNTY5LjA4ODAgVGQNCiggQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgKSBUag0KRVQNCkJUDQovRjEgMDAxMCBUZg0KNjkuMjUwMCA1NTcuMTM2MCBUZA0KKCB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBFdmVuIG1vcmUuIENvbnRpbnVlZCBvbiBwYWdlIDIgLi4uKSBUag0KRVQNCmVuZHN0cmVhbQ0KZW5kb2JqDQoNCjYgMCBvYmoNCjw8DQovVHlwZSAvUGFnZQ0KL1BhcmVudCAzIDAgUg0KL1Jlc291cmNlcyA8PA0KL0ZvbnQgPDwNCi9GMSA5IDAgUiANCj4+DQovUHJvY1NldCA4IDAgUg0KPj4NCi9NZWRpYUJveCBbMCAwIDYxMi4wMDAwIDc5Mi4wMDAwXQ0KL0NvbnRlbnRzIDcgMCBSDQo+Pg0KZW5kb2JqDQoNCjcgMCBvYmoNCjw8IC9MZW5ndGggNjc2ID4+DQpzdHJlYW0NCjIgSg0KQlQNCjAgMCAwIHJnDQovRjEgMDAyNyBUZg0KNTcuMzc1MCA3MjIuMjgwMCBUZA0KKCBTaW1wbGUgUERGIEZpbGUgMiApIFRqDQpFVA0KQlQNCi9GMSAwMDEwIFRmDQo2OS4yNTAwIDY4OC42MDgwIFRkDQooIC4uLmNvbnRpbnVlZCBmcm9tIHBhZ2UgMS4gWWV0IG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gKSBUag0KRVQNCkJUDQovRjEgMDAxMCBUZg0KNjkuMjUwMCA2NzYuNjU2MCBUZA0KKCBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSB0ZXh0LiBBbmQgbW9yZSApIFRqDQpFVA0KQlQNCi9GMSAwMDEwIFRmDQo2OS4yNTAwIDY2NC43MDQwIFRkDQooIHRleHQuIE9oLCBob3cgYm9yaW5nIHR5cGluZyB0aGlzIHN0dWZmLiBCdXQgbm90IGFzIGJvcmluZyBhcyB3YXRjaGluZyApIFRqDQpFVA0KQlQNCi9GMSAwMDEwIFRmDQo2OS4yNTAwIDY1Mi43NTIwIFRkDQooIHBhaW50IGRyeS4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gQW5kIG1vcmUgdGV4dC4gKSBUag0KRVQNCkJUDQovRjEgMDAxMCBUZg0KNjkuMjUwMCA2NDAuODAwMCBUZA0KKCBCb3JpbmcuICBNb3JlLCBhIGxpdHRsZSBtb3JlIHRleHQuIFRoZSBlbmQsIGFuZCBqdXN0IGFzIHdlbGwuICkgVGoNCkVUDQplbmRzdHJlYW0NCmVuZG9iag0KDQo4IDAgb2JqDQpbL1BERiAvVGV4dF0NCmVuZG9iag0KDQo5IDAgb2JqDQo8PA0KL1R5cGUgL0ZvbnQNCi9TdWJ0eXBlIC9UeXBlMQ0KL05hbWUgL0YxDQovQmFzZUZvbnQgL0hlbHZldGljYQ0KL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcNCj4+DQplbmRvYmoNCg0KMTAgMCBvYmoNCjw8DQovQ3JlYXRvciAoUmF2ZSBcKGh0dHA6Ly93d3cubmV2cm9uYS5jb20vcmF2ZVwpKQ0KL1Byb2R1Y2VyIChOZXZyb25hIERlc2lnbnMpDQovQ3JlYXRpb25EYXRlIChEOjIwMDYwMzAxMDcyODI2KQ0KPj4NCmVuZG9iag0KDQp4cmVmDQowIDExDQowMDAwMDAwMDAwIDY1NTM1IGYNCjAwMDAwMDAwMTkgMDAwMDAgbg0KMDAwMDAwMDA5MyAwMDAwMCBuDQowMDAwMDAwMTQ3IDAwMDAwIG4NCjAwMDAwMDAyMjIgMDAwMDAgbg0KMDAwMDAwMDM5MCAwMDAwMCBuDQowMDAwMDAxNTIyIDAwMDAwIG4NCjAwMDAwMDE2OTAgMDAwMDAgbg0KMDAwMDAwMjQyMyAwMDAwMCBuDQowMDAwMDAyNDU2IDAwMDAwIG4NCjAwMDAwMDI1NzQgMDAwMDAgbg0KDQp0cmFpbGVyDQo8PA0KL1NpemUgMTENCi9Sb290IDEgMCBSDQovSW5mbyAxMCAwIFINCj4+DQoNCnN0YXJ0eHJlZg0KMjcxNA0KJSVFT0YNCg==";
}
