<?php

namespace Lara\Jarvis\Database\Seeders;

use Illuminate\Database\Seeder;
use Lara\Jarvis\Models\Holiday;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "name" => "Confraternização Universal",
                "date" => "2022-01-01",
                "type" => 2,
            ],
            [
                "name" => "Véspera de ano novo",
                "date" => "2021-12-31",
                "type" => 1,
            ],
            [
                "name" => "Natal",
                "date" => "2021-12-25",
                "type" => 2,
            ],
            [
                "name" => "Proclamação da República",
                "date" => "2021-11-15",
                "type" => 2,
            ],
            [
                "name" => "Finados",
                "date" => "2021-11-02",
                "type" => 2,
            ],
            [
                "name" => "Nossa Sr.a Aparecida - Padroeira do Brasil",
                "date" => "2021-10-12",
                "type" => 2,
            ],
            [
                "name" => "Independência do Brasil",
                "date" => "2021-09-07",
                "type" => 2,
            ],
            [
                "name" => "Independência da Bahia",
                "date" => "2021-07-02",
                "type" => 1,
            ],
            [
                "name" => "São João",
                "date" => "2021-06-24",
                "type" => 1,
            ],
            [
                "name" => "Corpus Christi",
                "date" => "2021-06-03",
                "type" => 2,
            ],
            [
                "name" => "Dia do Trabalho",
                "date" => "2021-05-01",
                "type" => 2,
            ],
            [
                "name" => "Tiradentes",
                "date" => "2021-04-21",
                "type" => 2,
            ],
            [
                "name" => "Paixão de Cristo",
                "date" => "2021-04-02",
                "type" => 2,
            ],
            [
                "name" =>"Carnaval",
                "date" => "2021-02-16",
                "type" => 2,
            ],
            [
                "name" => "Carnaval",
                "date" => "2021-02-15",
                "type" => 2,
            ],
            [
                "name" => "Confraternização Universal",
                "date" => "2021-01-01",
                "type" => 2,
            ],
        ];

        array_walk($data, function ($item) {
            Holiday::updateOrCreate($item);
        });
    }
}
