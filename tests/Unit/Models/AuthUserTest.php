<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\AuthUser;

class AuthUserTest extends TestCase
{

    /**
     * AUthUserのテスト
     */
    public function test_attribute()
    {

        $ary = [
            'abc' => 123,
            'def' => 456
        ];

        $model = new AuthUser($ary);

        $this->assertEquals($model->getAuthIdentifierName(), 'userId');
        $this->assertIsArray($model->getAuthIdentifier());
        $this->assertEquals($model->abc, 123);

        $model->def = 567;
        $this->assertEquals($model->def, 567);

        $this->assertTrue(isset($model->def));


    }
}
