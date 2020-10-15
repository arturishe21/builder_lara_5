<?php

namespace Vis\Builder\Tests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Group;

abstract class BaseClassTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    protected function authAdmin()
    {
        $user = factory(User::class)->create([]);
        $group = factory(Group::class)->create();

         $user->activation()->create([
            'completed' => 1,
            'code' => 'asdasd',
            'completed_at' => ''
        ]);

        $group->users()->attach($user);

        \Sentinel::login($user);

        return $user;
    }
}
