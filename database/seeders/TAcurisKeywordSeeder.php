<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * テスト用Acuris検索履歴
 */
class TAcurisKeywordSeeder extends Seeder
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
                $startDate = new \DateTime('2022-12-01');
                $endDate = new \DateTime('2023-03-01');
                $interval = new \DateInterval('P1D');

                $period = new \DatePeriod($startDate, $interval, $endDate);

                foreach ($period as $date) {

                    DB::table('tAcurisKeywordHistory')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'userId' => sprintf('jcis-ent%02d-%03d', $companyId, $userId),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'searchCount' => 10,
                        'lookupCount' => 5,
                        'errSearchCount' => 0,
                        'errLookupCount' => 0,
                    ]);

                }

            }

        }

    }

}
