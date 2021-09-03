<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * テスト用ユーザー
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i = 0; $i <= 25; $i++) {

            // ユーザーマスタ
            DB::table('mUserCompany')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'name' => sprintf('株式会社アントレンド%02d', $i),
                'postCode' => '1080023',
                'address' => '東京都港区芝浦2-14-13 MCK芝浦ビル6F',
                'tel' => '03-5444-2500',
                'staffName' => sprintf('担当%02d', $i),
                'staffDepartmentJob' => '開発部',
                'staffTel' => '03-5444-2501',
                'staffMail' => 'naoki_hagiwara@entrend.net',
                'claimName' => '経理部担当者',
                'claimDepartmentJob' => '経理部',
                'claimTel' => '03-5444-2502',
                'claimMailTo' => 'keiri@entrend.net,naoki_hagiwara@entrend.net',
                'claimMailCc' => 'keiri_cc@entrend.net,naoki_hagiwara@entrend.net',
                'chargeName' => '窓口担当者',
                'chargeMail' => 'mado@entrend.net',
                'contractStatus' => 1,
                'delFlg' => 0,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // 契約プラン
            // WEB
            DB::table('tContractPlan')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => '2',
                'contractTypeId' => '1',
                'startTrial' => '2021-07-01',
                'useStartDate' => '2021-08-01',
                'useUpdateDate' => '2021-08-01',
                'useEndAlertDate' => '2021-09-01',
                'useEndDate' => '2021-10-01',
                'idUnitPrice' => 10000,
                'searchUnitPrice' => 290,
                'searchCount' => 1000,
                'deposit' => 290000,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // API
            DB::table('tContractPlan')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => '5',
                'contractTypeId' => '1',
                'startTrial' => '2021-07-01',
                'useStartDate' => '2021-08-01',
                'useUpdateDate' => '2021-08-01',
                'useEndAlertDate' => '2021-09-01',
                'useEndDate' => '2021-10-01',
                'idUnitPrice' => 10000,
                'searchUnitPrice' => 290,
                'searchCount' => 1000,
                'deposit' => 290000,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // ユーザー詳細
            // WEB
            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => '2',
                'userId' => sprintf('jcis-ent%02d-001', $i),
                'password' => sprintf('ent%02d-001', $i),
                'name' => 'WEB担当者01',
                'departmentJob' => '開発部',
                'mail' => 'jcis-ent00-001@entrend.net',
                'loginDatetime' => date('Y/m/d h:i:s'),
                'lockFlg' => 0,
                'delFlg' => 0,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => '2',
                'userId' => sprintf('jcis-ent%02d-002', $i),
                'password' => sprintf('ent%02d-002', $i),
                'name' => 'WEB担当者02',
                'departmentJob' => '総務部',
                'mail' => 'jcis-ent00-002@entrend.net',
                'loginDatetime' => date('Y/m/d h:i:s'),
                'lockFlg' => 0,
                'delFlg' => 0,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            // API
            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => '5',
                'userId' => sprintf('jcisapi-ent%02d-001',$i),
                'password' => sprintf('ent%02d-001', $i),
                'name' => 'API担当者01',
                'departmentJob' => '開発部',
                'mail' => 'jcisapi-ent00-001@entrend.net',
                'loginDatetime' => date('Y/m/d h:i:s'),
                'lockFlg' => 0,
                'delFlg' => 0,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);

            DB::table('mUserDetail')->insert([
                'companyId' => sprintf('ent%02d', $i),
                'contractPlanId' => '5',
                'userId' => sprintf('jcisapi-ent%02d-002',$i),
                'password' => sprintf('ent%02d-002', $i),
                'name' => 'API担当者02',
                'departmentJob' => '開発部',
                'mail' => 'jcisapi-ent00-002@entrend.net',
                'loginDatetime' => date('Y/m/d h:i:s'),
                'lockFlg' => 0,
                'delFlg' => 0,
                'createDatetime' => date('Y/m/d h:i:s'),
                'updateDatetime' => date('Y/m/d h:i:s')
            ]);
        }

    }

}
