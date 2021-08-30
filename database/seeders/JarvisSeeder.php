<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JarvisSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DummySeeder::class);
    }
}
