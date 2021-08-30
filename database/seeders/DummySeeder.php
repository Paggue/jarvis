<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lara\Jarvis\Models\State;

class DummySeeder extends Seeder
{
    public function run ()
    {
        State::updateOrCreate([
            'name' => "Gibberish",
            'uf' => "ZZZZ"
        ]);
    }
}
