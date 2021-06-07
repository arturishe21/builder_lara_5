<?php

namespace App\Models;

use Cartalyst\Sentinel\Roles\EloquentRole;

class Group extends EloquentRole
{
    protected $table = 'roles';

    public function t($ident)
    {
        return $this->$ident;
    }
}
