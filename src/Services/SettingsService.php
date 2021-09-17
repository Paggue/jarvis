<?php


namespace Lara\Jarvis\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lara\Jarvis\Models\Setting;
use Lara\Jarvis\Validators\SettingValidator;

class SettingsService
{
    use ServiceTrait;

    protected $parent_id;

    public function model ()
    {
        return new Setting();
    }

    public function validationRules ()
    {
        return SettingValidator::class;
    }

    public function setId ($id)
    {
        $this->parent_id = $id;

        return $this;
    }


    public function index (Request $request)
    {
        return Setting::publicConfig();
    }

    public function update (Request $request)
    {
        $this->validationRules()::validate($request->all());

        DB::transaction(function () use ($request) {

            foreach ($request->except(Setting::PRIVATE_KEYS) as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                Setting::updateOrCreate(['key' => preg_replace('/\s+/', '', $key)], [
                    'value' => $value,
                ]);
            }
        });

        return Setting::publicConfig();
    }
}
