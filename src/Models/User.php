<?php

namespace Vis\Builder\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use App\Models\Group;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends EloquentUser
{
    protected $table = 'users';

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'role_users',  'user_id', 'role_id');
    }

    public function activation(): HasOne
    {
        return $this->hasOne(EloquentActivation::class);
    }

    public function setFillable(array $params): void
    {
        $this->fillable = $params;
    }

    public function getAvatar(array $imgParam): string
    {
        $image = $this->picture ?? '/packages/vis/builder/img/blank_avatar.gif';

        return glide($image, $imgParam);
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function hasAccessForCms(string $link, string $action = 'view'): mixed
    {
        $link = str_replace(['/'], [''], $link).'.'. $action;

        return $this->hasAccess([$link]);
    }

    public function hasAccessActionsForCms(?string $action): mixed
    {
        $urlArray =  explode('/', request()->path());

        $url = last($urlArray);

        if (request()->is('*/groups') || request()->has('foreign_field')) {
            return true;
        }

        return $this->hasAccessForCms($url, $action);
    }
}
