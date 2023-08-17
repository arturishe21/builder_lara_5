<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revision extends Model
{
    protected $table = 'revisions';

    protected $fillable = ['revisionable_type', 'revisionable_id', 'user_id', 'key', 'old_value', 'new_value'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
