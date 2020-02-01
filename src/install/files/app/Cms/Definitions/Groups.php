<?php

namespace App\Cms\Definitions;

use App\Models\Group;
use Vis\Builder\Services\Actions;
use Vis\Builder\Fields\{
    Id,
    Text,
    Permissions
};

use Vis\Builder\Definitions\Resource;

class Groups extends Resource
{
    public $model = Group::class;
    public $title = 'Группы';

    public function fields()
    {
        return [
            'Общая' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Имя', 'slug')->filter(),
                Text::make('Название', 'name')->filter(),
            ],
            'Права доступа' => [
                Permissions::make('Доступы', 'permissions')->onlyForm(),
            ]
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
