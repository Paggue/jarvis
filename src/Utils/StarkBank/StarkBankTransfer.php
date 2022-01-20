<?php


namespace Lara\Jarvis\Utils\StarkBank;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use phpDocumentor\Reflection\File;
use StarkBank\Project;
use StarkBank\Settings;
use StarkBank\Transfer;

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
class StarkBankTransfer
{
    /**
     * @param $data
     * @return array
     * @throws ValidationException
     */
    public static function create ($data)
    {
        $banksTable = $data['banks_table'] ?? 'banks';

        $validator = Validator::make($data, [
            'id'           => 'required|string', // Unique ID to avoid duplicate transactions.
            'holder'       => 'required|string',
            'document'     => 'required|cpf_cnpj',
            'amount'       => 'required|numeric',
            'ispb'         => "required|numeric|exists:$banksTable,ispb",
            'agency'       => 'required|numeric',
            //            'agency_digit'  => 'numeric',
            'account'      => 'required|numeric',
            'account_type' => 'required|string',
            //            'account_digit' => 'numeric',
            'tags'         => 'required|array',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);


        $transfer = new Transfer([
            "amount"        => $data['amount'],
            "bankCode"      => $data['ispb'],
            "taxId"         => $data['document'],
            "name"          => $data['holder'],
            "branchCode"    => isset($data['agency_digit']) ? "{$data['agency']}-{$data['agency_digit']}" : (string)$data['agency'],
            "accountNumber" => isset($data['account_digit']) ? "{$data['account']}-{$data['account_digit']}" : "{$data['account']}-0",
            "externalId"    => $data['id'], // Unique ID to avoid duplicate transactions.
            "scheduled"     => Carbon::now(),
            "accountType"   => $data['account_type'] == 'cc' ? 'checking' : 'savings',
            "tags"          => $data['tags']
        ]);

        $transfers = Transfer::create([$transfer]);

        return $transfers;
    }

    /**
     * @param $id
     * @return array
     */
    public static function show ($id)
    {
        return Transfer::get($id);
    }

    /**
     * @param $id
     * @return File
     */
    public static function pdf ($id)
    {
        return Transfer::pdf($id);
    }
}
