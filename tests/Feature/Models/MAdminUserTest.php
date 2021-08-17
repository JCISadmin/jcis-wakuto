<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\MAdminUser;

class MAdminUserTest extends TestCase
{
    /**
     * 認証メソッドのテスト
     */
    public function test_getUserCredentials()
    {

        $model = new MAdminUser();

        $ret = $model->getUserCredentials('admin', '0000');
        $this->assertEquals($ret->userId, 'admin');

        $ret = $model->getUserCredentials('admin', '0001');
        $this->assertNull($ret);

    }
}
