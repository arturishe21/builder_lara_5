<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;

class Translations extends Model
{
    protected $table = 'translations';
    public $timestamps = false;
    protected $guarded = ['id'];
}
