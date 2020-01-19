<?php

namespace App\Cms\Definitions;

use App\Models\User;
use Carbon\Carbon;
use Vis\Builder\Services\Actions;
use Vis\Builder\Fields\{
    ManyToMany,
    Readonly,
    Relations\Options,
    Password,
    Checkbox,
    Id,
    Text
};

use Vis\Builder\Definitions\Resource;

class Users extends Resource
{
    public $model = User::class;
    public $title = 'Пользователи';
    protected $orderBy = 'created_at desc';

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
                Readonly::make('Дата регистрации', 'created_at')->default(Carbon::now())->sortable(),
                Readonly::make('Дата последнего входа', 'last_login')->sortable()
            ],

            'Группа' => [
                ManyToMany::make('Группа')->options(
                    (new Options('groups'))->keyField('name')
                ),
            ]

        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
