<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = ['id_user', 'ip_user', 'message', 'model', 'id_record', 'action'];
}
