<?php


namespace Lara\Jarvis\Utils\StarkBank;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use StarkBank\Deposit;
use StarkBank\Event;
use StarkBank\Invoice;
use StarkBank\Project;
use StarkBank\Settings;
use StarkBank\Balance;
use StarkBank\Webhook;

$project = new Project([
    "environment" => config('jarvis.starkbank.environment'),
    "id"          => config('jarvis.starkbank.project_id'),
    "privateKey"  => config('jarvis.starkbank.token')
]);

Settings::setLanguage("pt-BR");
Settings::setUser($project);

/**
 * Class StarkBank
 * @package App\Utils
 */
class StarkBank
{
    public static function getBalance ()
    {
        return Balance::get()->amount;
    }

    public static function createInvoice ($data)
    {
        $validator = Validator::make($data, [
            'amount'   => 'required|integer',
            'document' => 'required|string',
            //            'due' => 'required|string',
            'name'     => 'required|string',
            //            'expiration' => 'required|string',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        $invoices = [new Invoice([
            "amount"     => $data['amount'],
            "taxId"      => $data['document'],
            "due"        => Carbon::now()->toDateTimeString(),
            "name"       => $data['name'],
            "expiration" => Carbon::now()->days(30)->timestamp
        ])];

        return Invoice::create($invoices);
    }

    public static function webhook ()
    {
        $webhooks = Webhook::query();
        $data     = [];
        foreach ($webhooks as $webhook) {
            $data[] = $webhook;
        }

        return $data;
    }

    public static function event ()
    {
        $events = Event::query([
            "isDelivered" => false,
            "after"       => "2021-04-01",
            "before"      => "2021-06-30"
        ]);

        $data = [];
        foreach ($events as $event) {
            $data[] = $event;
        }

        return $data;
    }

    public static function deposits ()
    {
        $deposits = iterator_to_array(
            Deposit::query([
                "limit" => 1,
            ])
        );
        $data     = [];
        foreach ($deposits as $event) {
            $data[] = $event;
        }

        return $data;
    }

    public static function deposit ($id)
    {
        return Deposit::get($id);
    }
}
