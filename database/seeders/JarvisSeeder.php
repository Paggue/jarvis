<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lara\Jarvis\Database\Seeders\BankSeeder;
use Lara\Jarvis\Database\Seeders\HolidaySeeder;

class JarvisSeeder extends Seeder
{

    public function run()
    {
        $this->call(DummySeeder::class);
        $this->call(BankSeeder::class);
        $this->call(HolidaySeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
