<?php

namespace Tests\Feature;

use App\Models\BaseModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class uniCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $testData = [
            [
                'halfWidth' => '0123456789',
                'fullWidth' => '０１２３４５６７８９'
            ],
            [
                'halfWidth' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'fullWidth' => 'ＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺ'
            ],
            [
                'halfWidth' => 'abcdefghijklmnopqrstuvwxyz',
                'fullWidth' => 'ａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ'
            ],
            [
                'halfWidth' => 'ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜｦﾝ',
                'fullWidth' => 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン'
            ],
            [
                'halfWidth' => 'ｳﾞｶﾞｷﾞｸﾞｹﾞｺﾞｻﾞｼﾞｽﾞｾﾞｿﾞﾀﾞﾁﾞﾂﾞﾃﾞﾄﾞﾊﾞﾋﾞﾌﾞﾍﾞﾎﾞﾊﾟﾋﾟﾌﾟﾍﾟﾎﾟｧｨｩｪｫｬｭｮｯｰﾞﾟ､｡･｢｣',
                'fullWidth' => 'ヴガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポァィゥェォャュョッー◌゙◌゚、。・「」'
            ],
            [
                'halfWidth' => '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~',
                'fullWidth' => '！＂＃＄％＆＇（）＊＋，－．／：；＜＝＞？＠［＼］＾＿｀｛｜｝～'
            ],
            [
                'halfWidth' => '⦅⦆¢£¬¯¦¥₩￨￩￪￫￬￭￮',
                'fullWidth' => '｟｠￠￡￢￣￤￥￦│←↑→↓■○'
            ]
        ];

        $baseModel = new BaseModel();

        foreach($testData as $string){

            //['fullWidth']['halfWidth'] をUniCaseに変換し比較
            $fullWStr = $baseModel->convertToUniCase($string['fullWidth']);
            $halfWStr = $baseModel->convertToUniCase($string['halfWidth']);

            $this->assertEquals($halfWStr, $fullWStr, $string['halfWidth']);
        }
    }
}
