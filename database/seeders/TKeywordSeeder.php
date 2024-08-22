<?php

namespace Database\Seeders;

use DateTime;
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
        $userSeeder = new UserSeeder;
        $webPlanAry = $userSeeder->planAry;

        for ($companyId = 1; $companyId <= 6; $companyId++) {
            // userId
            for ($userId = 1; $userId <= 2; $userId++) {

                // 検索日時
                $startDate = new \DateTime('2020-01-01');
                $endDate = new \DateTime('2025-01-01');
                $interval = new \DateInterval('P1M');

                $period = new \DatePeriod($startDate, $interval, $endDate);

                $webContractPlanId = $webPlanAry[($companyId-1)%6]['before']['planId'];
                $depoFlg = $webPlanAry[($companyId-1)%6]['before']['typeId'] === 'allDepo' ? true : false;
                $trialFlg = true;
                foreach ($period as $date) {

                    // 2022/01/01以降 プランを変更
                    if ( $date >= new \DateTime('2022-01-01') ) {
                        $webContractPlanId = $webPlanAry[($companyId-1)%6]['after']['planId'];
                        $depoFlg = $webPlanAry[($companyId-1)%6]['after']['typeId'] === 'allDepo' ? true : false;
                    }

                    // 2021/01/01以降トライアル終了
                    if ( $date >= new \DateTime('2021-01-01') ) {
                        $trialFlg = false;
                    }

                    // WEB
                    DB::table('tKeywordHistory')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'contractPlanId' => $webContractPlanId,
                        'userId' => sprintf('jcis-ent%02d-%03d', $companyId, $userId),
                        'hash' => uniqid(),
                        'expireDate' => $date->format('YmdHisv'),
                        'keyword' => uniqid(),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'chargeFlg' => 0,
                    ]);
                    DB::table('tKeywordHistory')->insert([
                        'companyId' => sprintf('ent%02d', $companyId),
                        'contractPlanId' => $webContractPlanId,
                        'userId' => sprintf('jcis-ent%02d-%03d', $companyId, $userId),
                        'hash' => uniqid(),
                        'expireDate' => $date->format('YmdHisv'),
                        'keyword' => uniqid(),
                        'searchDate' =>  $date->format('Y-m-d'),
                        'chargeFlg' => 0,
                    ]);

                    // 直近3か月のみ検索履歴を追加
                    $now = new DateTime();
                    $diff = $now->diff($date);
                    $monthsDiff = ($diff->y * 12) + $diff->m;
                    if ($monthsDiff <= 2) {
                        for($i = 11; $i <= 25; $i++){
                            DB::table('tKeywordHistory')->insert([
                                'companyId' => sprintf('ent%02d', $companyId),
                                'contractPlanId' => $webContractPlanId,
                                'userId' => sprintf('jcis-ent%02d-%03d', $companyId, $userId),
                                'hash' => uniqid(),
                                'expireDate' => $date->format('YmdHisv'),
                                'keyword' => uniqid(),
                                'searchDate' =>  $date->format("Y-m-$i"),
                                'chargeFlg' => 0,
                            ]);

                            if($monthsDiff >= 2 && $i >= 15){
                                break;
                            }elseif($monthsDiff >= 1 && $i >= 20){
                                break;
                            }
                        }
                    }

                    // 全額デポ かつ トライアルでは無い時
                    if ($depoFlg === true && $trialFlg === false ) {
                        DB::table('tKeywordHistory')->insert([
                            'companyId' => sprintf('ent%02d', $companyId),
                            'contractPlanId' => $webContractPlanId,
                            'userId' => sprintf('jcis-ent%02d-%03d', $companyId, $userId),
                            'hash' => uniqid(),
                            'expireDate' => $date->format('YmdHisv'),
                            'keyword' => uniqid(),
                            'searchDate' =>  $date->format('Y-m-d'),
                            'chargeFlg' => 1,
                        ]);
                        DB::table('tKeywordHistory')->insert([
                            'companyId' => sprintf('ent%02d', $companyId),
                            'contractPlanId' => $webContractPlanId,
                            'userId' => sprintf('jcis-ent%02d-%03d', $companyId, $userId),
                            'hash' => uniqid(),
                            'expireDate' => $date->format('YmdHisv'),
                            'keyword' => uniqid(),
                            'searchDate' =>  $date->format('Y-m-d'),
                            'chargeFlg' => 1,
                        ]);
                    }

                    // API (2社のみ)
                    if ($companyId <= 2) {

                        DB::table('tKeywordHistory')->insert([
                            'companyId' => sprintf('ent%02d', $companyId),
                            'contractPlanId' => 'api',
                            'userId' => sprintf('jcisapi-ent%02d-%03d', $companyId, $userId),
                            'hash' => uniqid(),
                            'expireDate' => $date->format('YmdHisv'),
                            'keyword' => uniqid(),
                            'searchDate' =>  $date->format('Y-m-d'),
                            'chargeFlg' => 0,
                        ]);
                        DB::table('tKeywordHistory')->insert([
                            'companyId' => sprintf('ent%02d', $companyId),
                            'contractPlanId' => 'api',
                            'userId' => sprintf('jcisapi-ent%02d-%03d', $companyId, $userId),
                            'hash' => uniqid(),
                            'expireDate' => $date->format('YmdHisv'),
                            'keyword' => uniqid(),
                            'searchDate' =>  $date->format('Y-m-d'),
                            'chargeFlg' => 0,
                        ]);

                        // 直近3か月のみ検索履歴を追加
                        $now = new DateTime();
                        $diff = $now->diff($date);
                        $monthsDiff = ($diff->y * 12) + $diff->m;
                        if ($monthsDiff <= 2) {
                            for($i = 11; $i <= 25; $i++){
                                DB::table('tKeywordHistory')->insert([
                                    'companyId' => sprintf('ent%02d', $companyId),
                                    'contractPlanId' => 'api',
                                    'userId' => sprintf('jcisapi-ent%02d-%03d', $companyId, $userId),
                                    'hash' => uniqid(),
                                    'expireDate' => $date->format('YmdHisv'),
                                    'keyword' => uniqid(),
                                    'searchDate' =>  $date->format("Y-m-$i"),
                                    'chargeFlg' => 0,
                                ]);

                                if($monthsDiff >= 2 && $i >= 15){
                                    break;
                                }elseif($monthsDiff >= 1 && $i >= 20){
                                    break;
                                }
                            }
                        }

                        // デポ時 かつ トライアルでは無い時
                        if ($depoFlg === true && $trialFlg === false ) {
                            DB::table('tKeywordHistory')->insert([
                                'companyId' => sprintf('ent%02d', $companyId),
                                'contractPlanId' => 'api',
                                'userId' => sprintf('jcisapi-ent%02d-%03d', $companyId, $userId),
                                'hash' => uniqid(),
                                'expireDate' => $date->format('YmdHisv'),
                                'keyword' => uniqid(),
                                'searchDate' => $date->format('Y-m-d'),
                                'chargeFlg' => 1,
                            ]);
                            DB::table('tKeywordHistory')->insert([
                                'companyId' => sprintf('ent%02d', $companyId),
                                'contractPlanId' => 'api',
                                'userId' => sprintf('jcisapi-ent%02d-%03d', $companyId, $userId),
                                'hash' => uniqid(),
                                'expireDate' => $date->format('YmdHisv'),
                                'keyword' => uniqid(),
                                'searchDate' => $date->format('Y-m-d'),
                                'chargeFlg' => 1,
                            ]);
                        }

                    }
                }

            }

        }

    }

}
