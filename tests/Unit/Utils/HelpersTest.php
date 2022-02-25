<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Illuminate\Http\Request;
use Lara\Jarvis\Enums\Enums;
use Lara\Jarvis\Http\Resources\DefaultCollection;
use Lara\Jarvis\Models\City;
use Lara\Jarvis\Models\State;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\Helpers;

class HelpersTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function can_use_index_query_builder ()
    {
        $quantity = 10;

        $Request = Request::class;

        $state1 = State::factory()->create();

        $state2 = State::factory()->create();

        $cities1 = City::factory()->count($quantity / 2)->create([
            'state_id' => $state1->id,
        ]);

        $cities2 = City::factory()->count($quantity / 2)->create([
            'state_id' => $state2->id,
        ]);

        $result = Helpers::indexQueryBuilder(new $Request, ['state'], new City());

        $resourceCollection = DefaultCollection::class;

        $response = new $resourceCollection($result);

        $response = $response->collection;

        self::assertEquals($response->count(), $quantity);

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($cities1[$i]), json_encode($response[$i]));
        }

        for ($i = 5; $i < $quantity; ++$i) {
            self::assertJson(json_encode($cities2[$i - 5]), json_encode($response[$i]));
        }


        // STATE FILTER
        $query = "where[]=state_id,$state1->id";

        $request = Request::create("/whatever?$query", 'GET');

        $result = Helpers::indexQueryBuilder($request, ['state'], new City());

        $resourceCollection = DefaultCollection::class;

        $response = new $resourceCollection($result);

        $response = $response->collection;

        self::assertEquals($quantity / 2, $response->count());

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($cities1[$i]), json_encode($response[$i]));
        }


        // LIMIT
        $request = Request::create("/whatever", 'GET', [
            'limit' => 5
        ]);

        $result = Helpers::indexQueryBuilder($request, ['state'], new City());

        $resourceCollection = DefaultCollection::class;

        $response = new $resourceCollection($result);

        $response = $response->collection;

        self::assertEquals($quantity / 2, $response->count());

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($cities1[$i]), json_encode($response[$i]));
        }


        // WHERE ID
        $id    = $cities1[2]->id;
        $query = "where[]=id,$id";

        $request = Request::create("/whatever?$query", 'GET');

        $result = Helpers::indexQueryBuilder($request, ['state'], new City());

        $resourceCollection = DefaultCollection::class;

        $response = new $resourceCollection($result);

        $response = $response->collection;

        self::assertEquals(1, $response->count());

        self::assertJson(json_encode($cities1[1]), json_encode($response[0]));


        // LIKE NAME
        $city = City::factory()->create([
            'name'     => 'ZZZZZZZZZZZZZZZ',
            'state_id' => $state1->id,
        ]);

        $query = "like[]=name,$city->name";

        $request = Request::create("/whatever?$query", 'GET');

        $result = Helpers::indexQueryBuilder($request, ['state'], new City());

        $resourceCollection = DefaultCollection::class;

        $response = new $resourceCollection($result);

        $response = $response->collection;

        self::assertEquals(1, $response->count());

        self::assertJson(json_encode($city), json_encode($response[0]));


        // BETWEEN
        $date  = $city->created_at->format('Y-m-d');
        $query = "between[]=created_at,$date,$date";

        $request = Request::create("/whatever?$query", 'GET');

        $result = Helpers::indexQueryBuilder($request, ['state'], new City());

        $resourceCollection = DefaultCollection::class;

        $response = new $resourceCollection($result);

        $response = $response->collection;

        self::assertEquals($quantity + 1, $response->count());

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($cities1[$i]), json_encode($response[$i]));
        }

        for ($i = 5; $i < $quantity; ++$i) {
            self::assertJson(json_encode($cities2[$i - 5]), json_encode($response[$i]));
        }

        self::assertJson(json_encode($city), json_encode($response[$i]));


        // ORDER BY ID DESC
        $query = "order=id,desc";

        $request = Request::create("/whatever?$query", 'GET');

        $result = Helpers::indexQueryBuilder($request, ['state'], new City());

        $resourceCollection = DefaultCollection::class;

        $response = new $resourceCollection($result);

        $response = $response->collection;

        self::assertEquals($quantity + 1, $response->count());

        self::assertJson(json_encode($city), json_encode($response[$i]));

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($cities1[$i]), json_encode($response[$i]));
        }

        for ($i = 5; $i < $quantity; ++$i) {
            self::assertJson(json_encode($cities2[$i - 5]), json_encode($response[$i]));
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function can_paginate_collection ()
    {
        $quantity = 10;

        $state = State::factory()->create();

        $cities = City::factory()->count($quantity)->create([
            'state_id' => $state->id,
        ]);

        $result = City::all();

        $response = Helpers::paginateCollection($result);

        self::assertEquals($quantity, $response->total());

        for ($i = 0; $i < $quantity; ++$i) {
            self::assertJson(json_encode($response[$i]), json_encode($cities[$i]));
        }

        // WITH PER PAGE
        $response = Helpers::paginateCollection($result,$quantity / 2);

        self::assertEquals($quantity / 2, $response->perPage());

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($response[$i]), json_encode($cities[$i]));
        }


        // WITH PAGE NAME
        $pageName = "pagina";

        $response = Helpers::paginateCollection($result,$quantity / 2, $pageName);

        self::assertEquals($quantity / 2, $response->perPage());

        self::assertEquals($pageName, $response->getPageName());

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($response[$i]), json_encode($cities[$i]));
        }


        // WITH FRAGMENT
        $fragment = [
            'extra_data' => 'anything',
        ];

        $pageName = "pagina";

        $response = Helpers::paginateCollection($result,$quantity / 2, $pageName, $fragment);

        self::assertEquals($quantity / 2, $response->perPage());

        self::assertEquals($pageName, $response->getPageName());

        self::assertEquals($response->fragment(), $fragment);

        for ($i = 0; $i < $quantity / 2; ++$i) {
            self::assertJson(json_encode($response[$i]), json_encode($cities[$i]));
        }
    }

    /**
     * @test
     */
    public function can_calc_legal_entity ()
    {
        $document = "85975272505";

        $response = Helpers::legalEntity($document);

        self::assertEquals('PF', $response);

        $document = "29639420000102";

        $response = Helpers::legalEntity($document);

        self::assertEquals('PJ', $response);
    }

    /**
     * @test
     */
    public function can_convert_cents_to_money ()
    {
        $cents = 1;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "0,01");

        $cents = 10;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "0,10");

        $cents = 100;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "1,00");

        $cents = 1000;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "10,00");

        $cents = 10000;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "100,00");

        $cents = 100000;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "1000,00");

        $cents = 1000000;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "10000,00");

        $cents = 9;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "0,09");

        $cents = 99;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "0,99");

        $cents = 999;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "9,99");

        $cents = 9999;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "99,99");

        $cents = 99999;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "999,99");

        $cents = 999999;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "9999,99");

        $cents = 9999999;
        $money = Helpers::centsToMoney($cents);

        self::assertEquals($money, "99999,99");
    }

    /**
     * @test
     */
    public function can_generate_a_user_password ()
    {
        $password = Helpers::userPasswordGenerator();
        self::assertEquals(strlen($password), 6);
        self::assertTrue(strtolower($password) == $password);

        $password = Helpers::userPasswordGenerator(16);
        self::assertEquals(strlen($password), 16);
        self::assertTrue(strtolower($password) == $password);
    }

    /**
     * @test
     */
    public function can_sanitize_string ()
    {
        $str       = "aa23;42.-49";
        $sanitized = Helpers::sanitizeString($str);

        self::assertEquals($sanitized, "234249");

        $str       = "69022-588";
        $sanitized = Helpers::sanitizeString($str);

        self::assertEquals($sanitized, "69022588");

        $str       = "37.778.540/0001-00";
        $sanitized = Helpers::sanitizeString($str);

        self::assertEquals($sanitized, "37778540000100");

        $str       = "536.518.060-73";
        $sanitized = Helpers::sanitizeString($str);

        self::assertEquals($sanitized, "53651806073");

        $str       = "  xxx 420-20z42;23/217 s 092-;.mzz\\2";
        $sanitized = Helpers::sanitizeString($str);

        self::assertEquals($sanitized, "4202042232170922");

        $str       = "21 3213 42 - 424/ 4525";
        $sanitized = Helpers::sanitizeString($str);

        self::assertEquals($sanitized, "213213424244525");
    }

    /**
     * @test
     */
    public function can_sanitize_string_with_letters ()
    {
        $str       = "aa23;42.-49";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "aa234249");

        $str       = "69022-588";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "69022588");

        $str       = "37.778.540/0001-00";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "37778540000100");

        $str       = "536.518.060-73";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "53651806073");

        $str       = "  xxx 420-20z42;23/217 s 092-;.mzz\\2";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "xxx42020z4223217s092mzz2");

        $str       = "21 3213 42 - 424/ 4525";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "213213424244525");

        $str       = "MSG-4214";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "MSG4214");

        $str       = "MSG 4214";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "MSG4214");

        $str       = "MSG4214";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "MSG4214");

        $str       = "L$24S 9%434     42%3c cd s1K?`;/.~53LC SD9fs,.;d64%@#!fks ";
        $sanitized = Helpers::sanitizeStringWithLetters($str);

        self::assertEquals($sanitized, "L24S9434423ccds1K53LCSD9fsd64fks");
    }

    /**
     * @test
     */
    public function can_mask_string ()
    {
        $cpf       = '11111111111';
        $cpfMasked = Helpers::mask(Enums::MASKS['cpf'], $cpf);
        self::assertEquals('111.111.111-11', $cpfMasked);


        $cnpj       = '11111111111111';
        $cnpjMasked = Helpers::mask(Enums::MASKS['cnpj'], $cnpj);
        self::assertEquals('11.111.111/1111-11', $cnpjMasked);

        $zipCode       = '11111111';
        $zipCodeMasked = Helpers::mask(Enums::MASKS['zip_code'], $zipCode);
        self::assertEquals('11111-111', $zipCodeMasked);

        $phone       = '11111111111';
        $phoneMasked = Helpers::mask(Enums::MASKS['phone'], $phone);
        self::assertEquals('(11)11111-1111', $phoneMasked);
    }

    /**
     * @test
     */
    public function can_mask_documents ()
    {
        $cpf       = '11111111111';
        $cpfMasked = Helpers::maskDocument($cpf);
        self::assertEquals('111.111.111-11', $cpfMasked);


        $cnpj       = '11111111111111';
        $cnpjMasked = Helpers::maskDocument($cnpj);
        self::assertEquals('11.111.111/1111-11', $cnpjMasked);

        $random       = '11111';
        $randomMasked = Helpers::maskDocument($random);
        self::assertEquals(null, $randomMasked);
    }

    /**
     * @test
     */
    public function can_convert_number_to_text ()
    {
        $num     = 1;
        $numText = Helpers::numberToText($num);
        self::assertEquals("um", $numText);

        $num     = 2;
        $numText = Helpers::numberToText($num);
        self::assertEquals("dois", $numText);

        $num     = '1';
        $numText = Helpers::numberToText($num);
        self::assertEquals("um", $numText);

        $num     = '1';
        $numText = Helpers::numberToText($num, 'en');
        self::assertEquals("one", $numText);

        $num     = '1';
        $numText = Helpers::numberToText($num, 'zz');
        self::assertEquals("um", $numText);
    }

    /**
     * @test
     */
    public function can_convert_cents_to_text ()
    {
        $money     = 1;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Zero reais e um centavo', $moneyText);

        $money     = 2;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Zero reais e dois centavos', $moneyText);

        $money     = 100;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Um real', $moneyText);

        $money     = 101;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Um real e um centavo', $moneyText);

        $money     = 102;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Um real e dois centavos', $moneyText);

        $money     = 200;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Dois reais', $moneyText);

        $money     = 201;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Dois reais e um centavo', $moneyText);

        $money     = 202;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Dois reais e dois centavos', $moneyText);

        $money     = 0;
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Zero reais', $moneyText);

        $money = 'a';
        $moneyText = Helpers::centsToText($money);
        self::assertEquals('Zero reais', $moneyText);
    }
}
