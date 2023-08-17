<?php

namespace App\Cms\Definitions;

use App\Models\User;
use Carbon\Carbon;
use Vis\Builder\Http\Services\Actions;
use Vis\Builder\Http\Fields\{
    ManyToMany,
    ReadonlyField,
    Relations\Options,
    Password,
    Checkbox,
    Id,
    Text
};

use Vis\Builder\Http\Definitions\Resource;

class Users extends Resource
{
    public string $model = User::class;
    public string $title = 'Пользователи';
    protected string $orderBy = 'created_at desc';

    public function fields()
    {
        return [
            'Общая' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Email', 'email')->sortable()->filter(),
                Password::make('Пароль', 'password')->onlyForm(),
                Text::make('Фамилия', 'last_name')->sortable()->filter(),
                Text::make('Имя', 'first_name')->sortable()->filter(),
                Checkbox::make('Активен', 'completed')->hasOne('activation'),
                ReadonlyField::make('Дата регистрации', 'created_at')->default(Carbon::now())->sortable(),
                ReadonlyField::make('Дата последнего входа', 'last_login')->sortable()
            ],

            'Группа' => [
                ManyToMany::make('Группа')->options(
                    (new Options('groups'))->keyField('name')
                ),
            ]

        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->delete();
    }
}
