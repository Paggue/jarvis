<?php


namespace Lara\Jarvis\Services;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lara\Jarvis\Enums\BankAccountEnum;
use Lara\Jarvis\Enums\TagsEnum;
use Lara\Jarvis\Models\Bank;
use Lara\Jarvis\Models\BankAccount;
use Lara\Jarvis\Utils\StarkBank\StarkBankTransfer;
use Lara\Jarvis\Validators\BankAccountValidator;

class BankAccountService
{
    use ServiceTrait;

    protected $parent_id;
    protected $modelType;

    public function model ()
    {
        if (Auth::user()->isSuperAdmin() && !$this->parent_id)
            return new BankAccount();
        else
            return $this->modelType::findOrFail($this->parent_id)->bankAccounts();
    }

    public function bankAccountable ()
    {
        return $this->modelType::findOrFail($this->parent_id);
    }

    public function validationRules ()
    {
        return BankAccountValidator::class;
    }

    protected function relationships ()
    {
        return ["bank",];
    }

    public function setId ($id)
    {
        $this->parent_id = $id;

        return $this;
    }

    public function setModelType ($modelType)
    {
        $this->modelType = $modelType;

        return $this;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store (Request $request)
    {
        $result = null;

        DB::transaction(function () use ($request, &$result) {
            $data             = $request->all();
            $data["document"] = $this->bankAccountable()->document;
            $data["holder"]   = $this->bankAccountable()->legal_name;

            $this->validationRules()::validate(array_merge(['bank_accountable_id' => $this->parent_id], $data));
            $result = $this->model()->create($data);

            $transfers =  StarkBankTransfer::create(array_merge($data, [
                'id'     => (string)$result->id . 'UUID' . Str::uuid(),
                'amount' => 1, // R$0.01
                'ispb'   => Bank::find($result->bank_id)->ispb,
                'tags'   => [TagsEnum::CHECK_BANK_ACCOUNT]
            ]));

            $result->verifications++;
            $result->save();
        });

        return $result->load($this->relationships());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update (Request $request, $id)
    {
        $result = null;

        DB::transaction(function () use ($request, &$result, &$id) {
            $result = $this->model()->findOrFail($id);
            if ($result->verifications < 3) {
                $data             = $request->all();
                $data["document"] = $this->bankAccountable()->document;
                $data["status"]   = BankAccountEnum::STATUS['processing'];
                $data["info"]     = null;
                $data["holder"]   = $this->bankAccountable()->legal_name;
                $this->validationRules()::validate(array_merge(['id' => $id, 'bank_accountable_id' => $this->parent_id], $data));
                $result->update($data);

                StarkBankTransfer::create(array_merge($data, [
                    'id'     => (string)$result->id . 'UUID' . Str::uuid(),
                    'amount' => 1, // R$0.01
                    'ispb'   => Bank::find($result->bank_id)->ispb,
                    'tags'   => [TagsEnum::CHECK_BANK_ACCOUNT]
                ]));

                $result->verifications++;
                $result->save();
            } else {
                abort(422, "maximum number of updates exceeded");
            }
        });

        return $result->load($this->relationships());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function setMain (Request $request, $id)
    {
        $result = null;

        DB::transaction(function () use ($request, &$result, &$id) {
            $result = $this->model()->whereNotNull('verified_at')->findOrFail($id);

            $this->model()->update(['main_account' => false]);

            $result->main_account = true;
            $result->save();
        });

        return $result->load($this->relationships());
    }

    public static function check ($id, $event)
    {
        try {
            $bankAccount = BankAccount::findOrFail($id);

            DB::transaction(function () use ($bankAccount, $event) {
                $data = $event['transfer'];

                if (in_array($data['status'], ['success', 'processing']) && !$bankAccount->verified_at) {

                    if ($data['status'] == 'processing') {
                        $bankAccount->status = BankAccountEnum::STATUS['processing'];
                    }
                    elseif ($data['status'] == 'success') {
                        $bankAccount->status      = BankAccountEnum::STATUS['confirmed'];
                        $bankAccount->verified_at = Carbon::now();
                    }

                    $bankAccount->info = null;
                    $bankAccount->save();

                } elseif ($data['status'] == 'failed') {
                    $bankAccount->status = BankAccountEnum::STATUS['invalid'];
                    $bankAccount->info   = ['message' => $event['errors'][0]];
                    $bankAccount->save();
                }
            });


        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
