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
        //1
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

        //2
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '辺',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '辺',
            'convertCharacter' => '邉',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '辺',
            'convertCharacter' => '邊',
        ]);

        //3
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '斉',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '斉',
            'convertCharacter' => '齊',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '斉',
            'convertCharacter' => '齋',
        ]);

        //4
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '阿',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '阿',
            'convertCharacter' => '安',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '阿',
            'convertCharacter' => '亜',
        ]);

        //5
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '伊',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '伊',
            'convertCharacter' => '居',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '伊',
            'convertCharacter' => '位',
        ]);

        //6
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '宇',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '宇',
            'convertCharacter' => '卯',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '宇',
            'convertCharacter' => '鵜',
        ]);

        //7
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '江',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '江',
            'convertCharacter' => '得',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '江',
            'convertCharacter' => '絵',
        ]);

        //8
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '尾',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '尾',
            'convertCharacter' => '御',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '尾',
            'convertCharacter' => '緒',
        ]);

        //9
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '加',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '加',
            'convertCharacter' => '火',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '加',
            'convertCharacter' => '可',
        ]);

        //10
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '木',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '木',
            'convertCharacter' => '黄',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '木',
            'convertCharacter' => '気',
        ]);

        //11
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '九',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '九',
            'convertCharacter' => '句',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '九',
            'convertCharacter' => '区',
        ]);

        //12
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '古',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '古',
            'convertCharacter' => '個',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '古',
            'convertCharacter' => '小',
        ]);

        //13
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '佐',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '佐',
            'convertCharacter' => '早',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '佐',
            'convertCharacter' => '差',
        ]);

        //14
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '市',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '市',
            'convertCharacter' => '詞',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '市',
            'convertCharacter' => '詩',
        ]);

        //15
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '須',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '須',
            'convertCharacter' => '素',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '須',
            'convertCharacter' => '巣',
        ]);

        //16
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '瀬',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '瀬',
            'convertCharacter' => '背',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '瀬',
            'convertCharacter' => '世',
        ]);

        //17
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '曽',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '曽',
            'convertCharacter' => '祖',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '曽',
            'convertCharacter' => '蘇',
        ]);

        //18
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '田',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '田',
            'convertCharacter' => '多',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '田',
            'convertCharacter' => '他',
        ]);

        //19
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '地',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '地',
            'convertCharacter' => '知',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '地',
            'convertCharacter' => '智',
        ]);

        //20
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '戸',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '戸',
            'convertCharacter' => '都',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '戸',
            'convertCharacter' => '徒',
        ]);

        //21
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '奈',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '奈',
            'convertCharacter' => '菜',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '奈',
            'convertCharacter' => '那',
        ]);

        //22
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '二',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '二',
            'convertCharacter' => '荷',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '二',
            'convertCharacter' => '似',
        ]);

        //23
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '根',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '根',
            'convertCharacter' => '音',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '根',
            'convertCharacter' => '値',
        ]);

        //24
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '野',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '野',
            'convertCharacter' => '之',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '野',
            'convertCharacter' => '乃',
        ]);

        //25
        DB::table('mConvertFont')->insert([
            'targetCharacter' => '葉',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '覇',
            'convertCharacter' => '破',
        ]);

        DB::table('mConvertFontDetail')->insert([
            'targetCharacter' => '覇',
            'convertCharacter' => '波',
        ]);

    }

}
