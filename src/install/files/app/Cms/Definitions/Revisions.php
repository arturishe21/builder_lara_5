<?php

namespace App\Cms\Definitions;

use Vis\Builder\Http\Services\Actions;
use Vis\Builder\Models\Revision;
use Vis\Builder\Http\Fields\{ForeignAjax, Id, Datetime, Text, Relations\Options};
use Vis\Builder\Http\Definitions\Resource;

class Revisions extends Resource
{
    public string $model = Revision::class;
    public string $title = 'Контроль изменений';
    protected string $orderBy = 'created_at desc';

    public function fields()
    {
        return [
            'test' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Модель' ,'revisionable_type')->filter(),
                Text::make('Id записи' ,'revisionable_id')->filter(),
                ForeignAjax::make('Пользователь', 'user_id')
                    ->options( (new Options('user'))->keyField('first_name'))
                    ->filter(),
                Text::make('Поле', 'key')->filter(),
                Text::make('Старое значение', 'old_value')->filter(),
                Text::make('Новое значение', 'new_value')->filter(),
                Datetime::make('Дата/Время', 'created_at')->filter(),
            ],
        ];
    }


    public function actions(): Actions
    {
        return Actions::make();
    }

}
