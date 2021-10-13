<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 契約状況マスタ
 */
class ContractStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mContractStatus')->insert([
            'contractStatus' => 1,
            'name' => 'トライアル',
        ]);

        DB::table('mContractStatus')->insert([
            'contractStatus' => 2,
            'name' => '契約中',
        ]);

        DB::table('mContractStatus')->insert([
            'contractStatus' => 3,
            'name' => '契約終了',
        ]);

    }
}
