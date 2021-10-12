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
            'contractPlanId' => 'trial',
            'name' => 'トライアル',
            'planType' => 'web',
            'idPrice' => 0,
            'unitPrice' => 290,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => 'small',
            'name' => '少額プラン',
            'planType' => 'web',
            'idPrice' => 5000,
            'unitPrice' => 350,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => 'normal',
            'name' => '標準プラン',
            'planType' => 'web',
            'idPrice' => 10000,
            'unitPrice' => 290,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => 'large',
            'name' => '大型プラン',
            'planType' => 'web',
            'idPrice' => 10000,
            'unitPrice' => 0,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => 'variation',
            'name' => '変動',
            'planType' => 'web',
            'idPrice' => 0,
            'unitPrice' => 0,
        ]);

        DB::table('mContractPlan')->insert([
            'contractPlanId' => 'api',
            'name' => 'APIプラン',
            'planType' => 'api',
            'idPrice' => 30000,
            'unitPrice' => 290,
        ]);

    }

}
