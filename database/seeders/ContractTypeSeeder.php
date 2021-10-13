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
            'contractTypeId' => 'allDepo',
            'name' => '全額デポジット',
        ]);

        DB::table('mContractType')->insert([
            'contractTypeId' => 'idDepo',
            'name' => 'ID代のみデポジット',
        ]);

        DB::table('mContractType')->insert([
            'contractTypeId' => 'allMonth',
            'name' => '毎月請求/トライアル',
        ]);

    }

}
