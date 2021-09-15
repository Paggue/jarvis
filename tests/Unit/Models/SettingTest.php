<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Models\Setting;
use Lara\Jarvis\Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_setting_has_key_and_name ()
    {
        $setting = Setting::factory()->create([
            'key'   => 'key',
            'value' => true,
        ]);

        self::assertEquals(true, $setting->value);
        self::assertEquals('key', $setting->key);
    }
}
