<?php


namespace Lara\Jarvis\Validators;

use Illuminate\Validation\Rule;

class BankAccountValidator
{
    use ValidatorTrait;

    protected function rules ($data = null)
    {
        $id = $data['id'] ?? null;

        return [
            'bank_id'       => 'required|numeric|exists:banks,id',
            'account_type'  => 'required|in:cc,cp',
            'agency'        => 'required|numeric',
            'agency_digit'  => 'numeric',
            'account_digit' => 'required|numeric',
            'operation'     => 'numeric',
            'main_account'  => 'boolean',

            'account' => [
                'required', 'numeric',
                Rule::unique('bank_accounts')->where(function ($query) use ($data, $id) {
                    return $query->where('account', $data['account'])
                        ->where('bank_id', $data['bank_id'])
                        ->where('agency', $data['agency'])
                        ->where('bank_accountable_id', $data['bank_accountable_id']);
                })->ignore($id, 'id'),
            ],
        ];
    }
}
