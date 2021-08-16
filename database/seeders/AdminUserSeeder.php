<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mAdminUser')->insert([
            'userId' => 'admin',
            'userName' => '管理者',
            'password' => '0000',
            'mail' => 'naoki_hagiwara@entrend.net',
            'createDatetime' => date('Y/m/d h:i:s'),
            'updateDatetime' => date('Y/m/d h:i:s')
        ]);

    }
}
