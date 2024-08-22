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
    }
}
