<?php

namespace Vis\Builder\Tests\Feature;

use Vis\Builder\Tests\BaseClassTest;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTest extends BaseClassTest
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNoAuthUser()
    {
        $response = $this->get('/admin/tree');

        $response->assertStatus(302)->assertRedirect('/login');
    }


    public function testAuthUser()
    {
        $this->authAdmin();

        $response = $this->get('/admin');

        $response->assertStatus(302)->assertRedirect('/admin/tree');
    }
}
