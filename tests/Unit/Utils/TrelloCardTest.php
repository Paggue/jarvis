<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\Helpers;
use Lara\Jarvis\Utils\TrelloCard;

class TrelloCardTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_trello_card ()
    {
        $balance      = 5000;
        $total_amount = 5000;

        $list_id = TrelloCard::ID_LIST_FALHAS;

        $data = [
            'name'      => '[Saldo Insuficiente] - Adicione saldo para processar os Pagamentos',
            'desc'      => "- **Disponivel**: R$ " . Helpers::centsToMoney($balance) .
                "\n- **Valor a pagar**: R$ " . Helpers::centsToMoney($total_amount),
            'label_ids' => [TrelloCard::LABEL_EMERGENCY]
        ];

        $expected = "unauthorized card permission requested";

        $response = TrelloCard::new($list_id, $data);

        self::assertEquals($expected, $response);
    }
}
