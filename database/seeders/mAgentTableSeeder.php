<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class mAgentTableSeeder extends Seeder
{
    /**
     * 代理店テーブル.
     *
     * @return void
     */
    public function run()
    {
        //日本信用情報サービス
        DB::table('mAgent')->insert([
            'distributor_cd' => '0000',
            'agent_cd' => '0000',
            'level' => '0',
            'status' => '0',
            'name' => '日本信用情報サービス株式会社',
            'postCode' => '2310023',
            'pref_code' => '14',
            'address' => '神奈川県横浜市中区山下町2番地　産業貿易センタービル9階',
            'tel' => '045-315-5408',
            'mailCompanyName' => '日本信用情報サービス',
            'homePageUrl' => 'https://www.jcis.co.jp/'
        ]);

        //日本リスク管理センター
        DB::table('mAgent')->insert([
            'distributor_cd' => '0000',
            'agent_cd' => 'jrmc',
            'level' => '1',
            'status' => '0',
            'name' => '日本リスク管理センター株式会社',
            'postCode' => '5400001',
            'pref_code' => '27',
            'address' => '大阪府大阪市中央区城見2丁目2番22号 マルイトOBPビル3F',
            'tel' => '06-6734-6277',
            'mailCompanyName' => '日本リスク管理センター株式会社',
            'homePageUrl' => 'https://www.j-rmc.co.jp/'
        ]);

        //日本信用情報サービス
        DB::table('mAgent')->insert([
            'distributor_cd' => '0000',
            'agent_cd' => 'jrms',
            'level' => '1',
            'status' => '0',
            'name' => '日本リスクマネ-ジメントサービス株式会社',
            'postCode' => '2310023',
            'pref_code' => '14',
            'address' => '神奈川県横浜市中区山下町2番地　産業貿易センタービル9階',
            'tel' => '045-315-5408',
            'mailCompanyName' => '日本リスクマネ-ジメントサービス株式会社',
            'homePageUrl' => 'https://www.jcis.co.jp/'
        ]);
    }
}
