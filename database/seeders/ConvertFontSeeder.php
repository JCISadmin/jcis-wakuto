<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 旧字体変換マスタ
 */
class ConvertFontSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '高',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '高',
            'convertCharacter' => '髙',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '高',
            'convertCharacter' => '喬',
        ]);

    }

}
