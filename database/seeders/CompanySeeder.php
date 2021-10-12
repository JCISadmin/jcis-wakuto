<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * テスト用ユーザー
 */
class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mCompany')->insert([
            'id' => 100,
            'name' => '日本信用情報サービス株式会社',
            'postCode' => '2310023',
            'address' => '神奈川県横浜市中区山下町2番地
産業貿易センター9階',
            'tel' => '045-550-5300',
            'fax' => '045-550-5566',
            'bank' => '三井住友銀行　日本橋支店　（普通）8355260　ニホンシンヨウジョウホウサービス（カ',
        ]);
    }
}