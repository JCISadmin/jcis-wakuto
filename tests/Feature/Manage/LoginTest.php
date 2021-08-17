<?php

namespace Tests\Feature\Manage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login()
    {
        $response = $this->get('/manage/login');
        $response->assertStatus(200);

        $response = $this->post('/manage/login', ['userId' => 'admin', 'password' => '0000']);
        $response->assertStatus(302);

        // TODO
        //$response->assertRedirect('zz');

    }
}
