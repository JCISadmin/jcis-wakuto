<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $hdsMode = env('HDS_MODE', 1);

        if ($hdsMode == 1) {

            $this->call([
                AdminUserSeeder::class,
                CompanySeeder::class,
                ContractPlanSeeder::class,
                ContractStatusSeeder::class,
                ContractTypeSeeder::class,
                ConvertFontSeeder::class,
                CorporationSeeder::class,
                PersonSeeder::class,
                PrefectureSeeder::class,
                TAcurisKeywordSeeder::class,
                TKeywordSeeder::class,
                TKeywordDetailSeeder::class,
                UserSeeder::class,
                VatSeeder::class,
            ]);

        } else {

            $this->call([
                AdminUserSeeder::class,
                CompanySeeder::class,
                ContractPlanSeeder::class,
                ContractStatusSeeder::class,
                ContractTypeSeeder::class,
                ConvertFontSeeder::class,
                CorporationSeeder::class,
                PersonSeeder::class,
                PrefectureSeeder::class,
                Agent\TAcurisKeywordSeeder::class,
                Agent\TKeywordSeeder::class,
                Agent\TKeywordDetailSeeder::class,
                Agent\UserSeeder::class,
                VatSeeder::class,
            ]);

        }
    }
}
