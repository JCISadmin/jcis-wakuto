<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 契約プランマスタ
 */
class ContractPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mContractPlan')->insert([
            'contractPlanId' => '1',
            'name' => '少額プラン',
            'planType' => 'web',
            'idPrice' => 5000,
            'unitPrice' => 350,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => '2',
            'name' => '標準プラン',
            'planType' => 'web',
            'idPrice' => 10000,
            'unitPrice' => 290,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => '3',
            'name' => '大型プラン',
            'planType' => 'web',
            'idPrice' => 11000,
            'unitPrice' => 250,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => '4',
            'name' => 'トライアル',
            'planType' => 'web',
            'idPrice' => 0,
            'unitPrice' => 290,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => '5',
            'name' => '標準プラン',
            'planType' => 'api',
            'idPrice' => 10000,
            'unitPrice' => 290,
        ]);

    }

}
