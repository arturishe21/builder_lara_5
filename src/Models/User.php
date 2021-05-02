<?php

namespace Vis\Builder;

use Cartalyst\Sentinel\Users\EloquentUser;
use DB;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use App\Models\Group;

/**
 * Class User.
 */
class User extends EloquentUser
{
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'role_users',  'user_id', 'role_id');
    }

    public function activation()
    {
        return $this->hasOne(EloquentActivation::class);
    }

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @param array $params
     */
    public function setFillable(array $params)
    {
        $this->fillable = $params;
    }

    /**
     * @param array $imgParam
     *
     * @return mixed|string
     */
    public function getAvatar(array $imgParam)
    {
        $image = $this->picture ?? '/packages/vis/builder/img/blank_avatar.gif';

        return glide($image, $imgParam);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name.' '.$this->last_name;
    }


    public function hasAccessForCms($link)
    {
        $link = str_replace(['/', '_'], [''], $link).'.view';

        return $this->hasAccess([$link]);
    }
}
