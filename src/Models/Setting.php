<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContracts;

class Setting extends Model implements AuditableContracts
{
    use HasFactory, Auditable;

    protected $fillable = ['key', 'value'];

    const PRIVATE_KEYS = [];

    protected static function newFactory ()
    {
        return \Lara\Jarvis\Database\Factories\SettingFactory::new();
    }

    public static function publicConfig ()
    {
        $settings = Setting::whereNotIn('key', self::PRIVATE_KEYS)->get();

        return Setting::convertBoolean($settings);
    }

    public static function adminConfig ()
    {
        return Setting::convertBoolean(Setting::all());
    }

    private static function convertBoolean ($settings)
    {
        $object = new \stdClass();

        $array = [];

        foreach ($settings as $key => $value) {
            $item = [];
            if ($value->key == 'true' || $value->value == 'false') {
                $item[$value->key] = ($value->value == 'true');
            } else {
                $item[$value->key] = $value->value;
            }
            $array[] = $item;
        }

        foreach ($array as $value) {
            foreach ($value as $key => $item) {
                $object->$key = $item;
            }
        }

        return $object;
    }
}
