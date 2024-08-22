<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * テスト用ユーザー
 */
class UserSeeder extends Seeder
{
    public $planAry = [
        [
            'name' => '全額デポ→IDデポ',
            'before' => [
                'planId' => 'normal',
                'typeId' => 'allDepo',
            ],
            'after' => [
                'planId' => 'small',
                'typeId' => 'idDepo',
            ],
        ],
        [
            'name' => '全額デポ→毎月請求',
            'before' => [
                'planId' => 'normal',
                'typeId' => 'allDepo',
            ],
            'after' => [
                'planId' => 'small',
                'typeId' => 'allMonth',
            ],
        ],
        [
            'name' => 'IDデポ→全額デポ',
            'before' => [
                'planId' => 'normal',
                'typeId' => 'idDepo',
            ],
            'after' => [
                'planId' => 'large',
                'typeId' => 'allDepo',
            ],
        ],
        [
            'name' => 'IDデポ→毎月請求',
            'before' => [
                'planId' => 'normal',
                'typeId' => 'idDepo',
            ],
            'after' => [
                'planId' => 'small',
                'typeId' => 'allMonth',
            ],
        ],
        [
            'name' => '毎月請求→全額デポ',
            'before' => [
                'planId' => 'normal',
                'typeId' => 'allMonth',
            ],
            'after' => [
                'planId' => 'large',
                'typeId' => 'allDepo',
            ],
        ],
        [
            'name' => '毎月請求→IDデポ',
            'before' => [
                'planId' => 'normal',
                'typeId' => 'allMonth',
            ],
            'after' => [
                'planId' => 'large',
                'typeId' => 'idDepo',
            ],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i = 1; $i <= 25; $i++) {

            // ユーザーマスタ
            DB::table('mUserCompany')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'name' => sprintf('株式会社アントレンド%02d %s', $i, $this->planAry[($i-1)%6]['name']),
                'kana' => sprintf('アントレンド%02d', $i),
                'postCode' => '1080023',
                'address' => '東京都港区芝浦2-14-13 MCK芝浦ビル6F',
                'tel' => '03-5444-2500',
                'staffName' => sprintf('担当者%02d', $i),
                'staffDepartmentJob' => sprintf('担当部%02d', $i),
                'staffTel' => '03-5444-2501',
                'staffMail' => 'naoki_hagiwara@entrend.net',
                'claimName' => '経理部担当者',
                'claimDepartmentJob' => '経理部',
                'claimTel' => '03-5444-2502',
                'claimMailTo' => 'keiri@entrend.net,naoki_hagiwara@entrend.net',
                'claimMailCc' => 'keiri_cc@entrend.net,naoki_hagiwara@entrend.net',
                'claimMailBcc' => 'keiri_bcc@entrend.net,naoki_hagiwara@entrend.net',
                'deliveryDate' => '3営業日以内',
                'paymentTerm' => 1,
                'freeFlg' => 0,
                'freePeriod' => 0,
                'chargeName' => sprintf('窓口担当者%02d', $i),
                'chargeMail' => sprintf('mado%02d@entrend.net', $i),
                'contractStatus' => 2,
                'delFlg' => 0,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // 契約プラン
            // WEB
            DB::table('tContractPlan')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => $this->planAry[($i-1)%6]['after']['planId'],
                'contractTypeId' => 0,
                'startTrial' => '2020-01-01',
                'useStartDate' => '2021-01-01',
                'useUpdateDate' => '2022-01-01',
                'useEndAlertDate' => '2023-12-01',
                'useEndDate' => '2024-12-31',
                'idUnitPrice' => 0,
                'searchUnitPrice' => 0,
                'searchCount' => 0,
                'deposit' => 100000,
                'trialSearchUnitPrice' => 290,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // API
            DB::table('tContractPlan')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => 'api',
                'contractTypeId' => 0,
                'startTrial' => '2020-01-01',
                'useStartDate' => '2021-01-01',
                'useUpdateDate' => '2022-01-01',
                'useEndAlertDate' => '2023-12-01',
                'useEndDate' => '2024-12-31',
                'idUnitPrice' => 0,
                'searchUnitPrice' => 0,
                'searchCount' => 0,
                'deposit' => 100000,
                'trialSearchUnitPrice' => 290,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // 契約プラン履歴
            // WEB
            DB::table('tContractPlanDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => $this->planAry[($i-1)%6]['before']['planId'],
                'seqNo' => 1,
                'contractTypeId' => $this->planAry[($i-1)%6]['before']['typeId'],
                'contractStartDate' => '2021-01-01',
                'contractEndDate' => '2021-12-31',
                'idUnitPrice' => 10000,
                'searchUnitPrice' => 100,
                'searchCount' => 100,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);
            DB::table('tContractPlanDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => $this->planAry[($i-1)%6]['after']['planId'],
                'seqNo' => 2,
                'contractTypeId' => $this->planAry[($i-1)%6]['after']['typeId'],
                'contractStartDate' => '2022-01-01',
                'contractEndDate' => '2024-12-31',
                'idUnitPrice' => 20000,
                'searchUnitPrice' => 200,
                'searchCount' => 200,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // API
            DB::table('tContractPlanDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => 'api',
                'seqNo' => 1,
                'contractTypeId' => $this->planAry[($i-1)%6]['after']['typeId'],
                'contractStartDate' => '2021-01-01',
                'contractEndDate' => '2021-12-31',
                'idUnitPrice' => 10000,
                'searchUnitPrice' => 100,
                'searchCount' => 100,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);
            DB::table('tContractPlanDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => 'api',
                'seqNo' => 2,
                'contractTypeId' => $this->planAry[($i-1)%6]['after']['typeId'],
                'contractStartDate' => '2022-01-01',
                'contractEndDate' => '2024-12-31',
                'idUnitPrice' => 20000,
                'searchUnitPrice' => 200,
                'searchCount' => 200,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // ユーザー詳細
            // WEB
            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => $this->planAry[($i-1)%6]['after']['planId'],
                'userId' => sprintf('jcis-ent%02d-001', $i),
                'password' => 0000,
                'name' => 'WEB担当者01',
                'departmentJob' => '開発部',
                'mail' => sprintf('jcis-ent%02d-001@entrend.net', $i),
                'idMailBcc' => 'id_bcc@entrend.net',
                'loginDatetime' => null,
                'lockFlg' => 0,
                'delFlg' => 0,
                'delMonth' => null,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);
            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => $this->planAry[($i-1)%6]['after']['planId'],
                'userId' => sprintf('jcis-ent%02d-002', $i),
                'password' => 0000,
                'name' => 'WEB担当者02',
                'departmentJob' => '開発部',
                'mail' => sprintf('jcis-ent%02d-002@entrend.net', $i),
                'idMailBcc' => 'id_bcc@entrend.net',
                'loginDatetime' => null,
                'lockFlg' => 0,
                'delFlg' => 0,
                'delMonth' => null,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // API
            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => 'api',
                'userId' => sprintf('jcisapi-ent%02d-001',$i),
                'password' => 0000,
                'name' => 'API担当者01',
                'departmentJob' => '開発部',
                'mail' => sprintf('jcisapi-ent%02d-001@entrend.net', $i),
                'idMailBcc' => 'id_bcc@entrend.net',
                'loginDatetime' => null,
                'lockFlg' => 0,
                'delFlg' => 0,
                'delMonth' => null,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);
            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => 'api',
                'userId' => sprintf('jcisapi-ent%02d-002',$i),
                'password' => 0000,
                'name' => 'API担当者02',
                'departmentJob' => '開発部',
                'mail' => sprintf('jcisapi-ent%02d-002@entrend.net', $i),
                'idMailBcc' => 'id_bcc@entrend.net',
                'loginDatetime' => null,
                'lockFlg' => 0,
                'delFlg' => 0,
                'delMonth' => null,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);
        }

    }

}
