<?php

namespace App\Cms\Definitions;

use App\Models\Group;
use Vis\Builder\Http\Services\Actions;
use Vis\Builder\Http\Fields\{
    Id,
    Text,
    Permissions
};

use Vis\Builder\Http\Definitions\Resource;

class Groups extends Resource
{
    public string $model = Group::class;
    public string $title = 'Группы';

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

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->delete();
    }
}
