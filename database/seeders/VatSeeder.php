<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 税率マスタ
 */
class VatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mVat')->insert([
            'startDate' => '2019-10-1',
            'endDate' => '3000-12-31',
            'tax' => '10',
        ]);
    }
}

