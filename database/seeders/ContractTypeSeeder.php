<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 契約形態
 */
class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mContractType')->insert([
            'contractTypeId' => '1',
            'name' => '全額デポジット',
        ]);

        DB::table('mContractType')->insert([
            'contractTypeId' => '2',
            'name' => 'ID代のみデポジット',
        ]);

        DB::table('mContractType')->insert([
            'contractTypeId' => '3',
            'name' => '毎月請求/トライアル',
        ]);

    }

}
