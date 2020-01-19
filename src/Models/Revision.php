<?php

namespace Vis\Builder;

use Illuminate\Database\Eloquent\Model;
use \App\Models\User;

class Revision extends Model
{
    protected $table = 'revisions';

    protected $fillable = ['revisionable_type', 'revisionable_id', 'user_id', 'key', 'old_value', 'new_value'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
