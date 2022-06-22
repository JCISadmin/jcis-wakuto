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
            //['fullWidth']を半角に変換し ['halfWidth']と比較
            $convertedName = $baseModel->convertToHalfWidth($string['fullWidth']);
            $this->assertEquals($string['halfWidth'],$convertedName, $string['fullWidth']);
        }
    }
}
