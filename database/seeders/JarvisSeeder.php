<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JarvisSeeder extends Seeder
{

    public function run()
    {
        $this->call(DummySeeder::class);
    }
}
