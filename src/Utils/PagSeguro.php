<?php

namespace Lara\Jarvis\Utils;

use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PagSeguro
{
    private $token;
    private $base_url;
    private $webhook_base_url;
    private $email;

    const TYPES = [
        'CREDIT_CARD'  => 1,
        'BILLET'       => 2,
        'ONLINE_DEBIT' => 3,
        'DEPOSIT'      => 7,
        'PIX'          => 11
    ];

    const STATUS = [
        'PAYMENT_PENDING' => 1,
        'ANALYSING'       => 2,
        'APPROVED'        => 3,
        'AVAILABLE'       => 4,
        'DISPUTE'         => 5,
        'RETURNED'        => 6,
        'CANCELLED'       => 7,
        'DEBITED'         => 8,
        'RETAIN'          => 9
    ];

    public function __construct ()
    {
        $this->token            = config('jarvis.pagseguro.token');
        $this->email            = config('jarvis.pagseguro.email');
        $this->base_url         = config('jarvis.pagseguro.base_url');
        $this->webhook_base_url = config('jarvis.pagseguro.webhook_base_url');
    }

    public function createBoleto ($data)
    {
        $data['notificationURL'] = $this->webhook_base_url . "/boleto";

        $validator = Validator::make($data, [
            'reference'        => 'required|string',
            'numberOfPayments' => 'required|string',
            'periodicity'      => 'required|string|in:monthly',
            'amount'           => 'required|string',
            'description'      => 'required|string',

            'customer.document.value' => 'required|string',
            'customer.document.type'  => 'required|string',
            'customer.name'           => 'required|string',
            'customer.email'          => 'required|string',
            'customer.phone.areaCode' => 'required|string',
            'customer.phone.number'   => 'required|string',

            'customer.address.postalCode' => 'string',
            'customer.address.street'     => 'string',
            'customer.address.number'     => 'string',
            'customer.address.district'   => 'string',
            'customer.address.city'       => 'string|exists:cities,name',
            'customer.address.state'      => 'string|exists:states,uf',
        ]);

        if ($validator->fails()) throw new ValidationException($validator);

        $response = Http::post($this->base_url . "/recurring-payment/boletos?email={$this->email}&token={$this->token}", $data);

        $body = json_decode($response->body());

        if (isset($body->errors)) abort(500, $body->errors[0]->message);
        else return json_decode($response->body());
    }

    /**
     * @see https://dev.pagseguro.uol.com.br/docs/api-notificacao-v1
     */
    public function getNotificationData ($code)
    {
        $response = Http::get($this->base_url . "/v3/transactions/notifications/${code}", [
            'email' => $this->email,
            'token' => $this->token
        ]);

        $simple_xml = new SimpleXMLElement($response->body());

        return json_decode(json_encode((array)$simple_xml), true);
    }
}
