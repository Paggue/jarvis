<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\FetchDocument;

class FetchDocumentTest extends TestCase
{
    /**
     * @test
     */
    public function can_fetch_a_cnpj_document ()
    {
        $data["document"] = "29639420000102";

        $expected = [
            'name'    => 'PAGGUE INTERMEDIACAO DE SERVICOS E NEGOCIOS LTDA',
            'alias'   => 'PAGGUE',
            'tax_id'  => '29639420000102',
            'type'    => 'MATRIZ',
            'founded' => '2018-02-06',
            'capital' => 50000,
            'email'   => 'paggue.com.br@gmail.com',
        ];

        $response = FetchDocument::get($data);

        self::assertJson(json_encode($expected), json_encode($response));
    }

    /**
     * @test
     */
    public function can_catch_status_different_from_active ()
    {
        $data["document"] = "07.447.819/0001-40";

        $expected = [
            'status' => 'INAPTA',
        ];

        $response = FetchDocument::get($data);

        self::assertJson(json_encode($response->registration), json_encode($expected));
    }

    /**
     * @test
     */
    public function will_throw_error_if_cnpj_not_found_at_revenue_service ()
    {
        $data["document"] = "86286877000106";

        $expected = [
            'error'   => '404',
            'message' => 'tax id is not registered at revenue service'
        ];

        $response = FetchDocument::get($data);

        self::assertJson(json_encode($response), json_encode($expected));
    }
}
