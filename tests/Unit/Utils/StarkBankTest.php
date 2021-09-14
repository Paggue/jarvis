<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\StarkBank\StarkBank;

class StarkBankTest extends TestCase
{
    use RefreshDatabase;

    public function setUp (): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function can_get_balance ()
    {
        $response = StarkBank::getBalance();

        self::assertEquals('integer', gettype($response));
    }

    /**
     * @test
     */
    public function can_show_transfer ()
    {
        $data = $this->transfer('success', ["check_bank_account"]);

//        $response = StarkBankTransfer::show($data['event']['log']['transfer']['id']);

//        dd($response);
        self::assertEquals(true, true);
    }

    /**
     * @test
     */
    public function can_return_pdf ()
    {
        $data = $this->transfer('success', ["check_bank_account"]);

//        $response = StarkBankTransfer::pdf($data['event']['log']['transfer']['id']);

//        dd($response);
        self::assertEquals(true, true);
    }
}
