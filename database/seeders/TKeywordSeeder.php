<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * テスト用検索履歴
 */
class TKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // WEB
        // companyId 2まで
        for ($companyId = 1; $companyId <= 2; $companyId++) {
            // userId
            for ($userId = 1; $userId <= 2; $userId++) {

                // 検索日時
                $startDate = new \DateTime('2020-01-01');
                $endDate = new \DateTime('2024-01-01');
                $interval = new \DateInterval('P1M');

                $period = new \DatePeriod($startDate, $interval, $endDate);

                foreach ($period as $date) {
                    
                    DB::table('tKeywordHistory')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'contractPlanId' => 'normal',
                        'userId' => sprintf('jcis-ent%02d-001', $userId),
                        'hash' => uniqid(),
                        'keyword' => uniqid(),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'chargeFlg' => 0,
                    ]);
                    DB::table('tKeywordHistory')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'contractPlanId' => 'normal',
                        'userId' => sprintf('jcis-ent%02d-001', $userId),
                        'hash' => uniqid(),
                        'keyword' => uniqid(),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'chargeFlg' => 0,
                    ]);
                    DB::table('tKeywordHistory')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'contractPlanId' => 'normal',
                        'userId' => sprintf('jcis-ent%02d-001', $userId),
                        'hash' => uniqid(),
                        'keyword' => uniqid(),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'chargeFlg' => 1,
                    ]);
                    DB::table('tKeywordHistory')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'contractPlanId' => 'normal',
                        'userId' => sprintf('jcis-ent%02d-001', $userId),
                        'hash' => uniqid(),
                        'keyword' => uniqid(),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'chargeFlg' => 1,
                    ]);
                }

            }

        }

    }

}
