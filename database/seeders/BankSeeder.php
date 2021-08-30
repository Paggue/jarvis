<?php

namespace Lara\Jarvis\Database\Seeders;

use Illuminate\Database\Seeder;
use Lara\Jarvis\Models\Bank;

class BankSeeder extends Seeder
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
                "name" => "Caixa Econômica",
                "code" => "104",
                "ispb" => "00360305"
            ],
            [
                "name" => "Brasil",
                "code" => "001",
                "ispb" => "00000000"
            ],
            [
                "name" => "Bradesco",
                "code" => "237",
                "ispb" => "60746948"
            ],
            [
                "name" => "Santander",
                "code" => "033",
                "ispb" => "90400888"
            ],
            [
                "name" => "Itaú",
                "code" => "341",
                "ispb" => "60701190"
            ],
            [
                "name" => "J. Safra S.A. ",
                "code" => "074",
                "ispb" => "03017677"
            ],
            [
                "name" => "Tribanco",
                "code" => "634",
                "ispb" => "17351180"
            ],
            [
                "name" => "Sicred",
                "code" => "748",
                "ispb" => "01181521"
            ],
            [
                "name" => "Citibank",
                "code" => "745",
                "ispb" => "33479023"
            ],
            [
                "name" => "Banese",
                "code" => "047",
                "ispb" => "13009717"
            ],
            [
                "name" => "Banrisul",
                "code" => "041",
                "ispb" => "92702067"
            ],
            [
                "name" => "Banco do Nordeste",
                "code" => "004",
                "ispb" => "07237373"
            ],
            [
                "name" => "Inter",
                "code" => "077",
                "ispb" => "00416968"
            ],
            [
                "name" => "Sicoob",
                "code" => "756",
                "ispb" => "02038232"
            ],
            [
                "name" => "Sicredi",
                "code" => "748",
                "ispb" => "01181521"
            ],
            [
                "name" => "BRB",
                "code" => "070",
                "ispb" => "00000208"
            ],
            [
                "name" => "Unicred",
                "code" => "136",
                "ispb" => "00315557"
            ],
            [
                "name" => "NuBank",
                "code" => "260",
                "ispb" => "18236120"
            ],
            [
                "name" => "PagSeguro Internet S.A",
                "code" => "290",
                "ispb" => "08561701"
            ],
            [
                "name" => "Neon",
                "code" => "655",
                "ispb" => "20855875"
            ],
            [
                "name" => "Original",
                "code" => "212",
                "ispb" => "92894922"
            ],
            [
                "name" => "Banco Pan",
                "code" => "623",
                "ispb" => "59285411"
            ],
            [
                "name" => "C6 S.A.",
                "code" => "336",
                "ispb" => "31872495"
            ],
            [
                "name" => "Stone pagamentos S.A",
                "code" => "197",
                "ispb" => "16501555"
            ],
            [
                "name" => "Itaucard",
                "code" => "345",
                "ispb" => "17192451"
            ],
            [
                "name" => "SAFRA S.A.",
                "code" => "422",
                "ispb" => "58160789"
            ],
            [
                "name" => "Next",
                "code" => "237",
                "ispb" => "60746948"
            ],
            [
                "name" => "Pic Pay",
                "code" => "380",
                "ispb" => "22896431"
            ],
            [
                "name" => "BMG S.A",
                "code" => "318",
                "ispb" => "61186680"
            ],
            [
                "name" => "CORA SCD S.A.",
                "code" => "403",
                "ispb" => "37880206"
            ]
        ];
        array_walk($data, function ($item) {
            Bank::updateOrCreate($item);
        });
    }
}
