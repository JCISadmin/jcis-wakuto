<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * テスト用同一ワード検索数
 */
class TKeywordDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // companyId
        for ($companyId = 1; $companyId <= 3; $companyId++) {
            // userId
            for ($userId = 1; $userId <= 2; $userId++) {

                // 検索日時
                $startDate = new \DateTime('2020-01-01');
                $endDate = new \DateTime('2025-01-01');
                $interval = new \DateInterval('P1M');

                $period = new \DatePeriod($startDate, $interval, $endDate);

                foreach ($period as $date) {

                    // WEB
                    DB::table('tKeywordHistoryDetail')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'userId' => sprintf('jcis-ent%02d-%03d', $companyId, $userId),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'searchCount' => 2,
                    ]);

                    // API
                    DB::table('tKeywordHistoryDetail')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'userId' => sprintf('jcisapi-ent%02d-%03d', $companyId, $userId),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'searchCount' => 2,
                    ]);

                }

            }

        }

    }

}
