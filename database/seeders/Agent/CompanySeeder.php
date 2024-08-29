<?php

namespace Database\Seeders\Agent;

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
            'id' => 'jrmc',
            'name' => '日本リスク管理センター株式会社',
            'postCode' => '5400001',
            'address' => '大阪府大阪市中央区城見2丁目2番22号 マルイトOBPビル3F',
            'tel' => '06-6734-6277',
            'fax' => '',
            'bank' => '',
            'mailCompanyName' => '日本リスク管理センター',
            'homePageUrl' => '',
        ]);
    }
}