<?php

namespace App\Cms\Definitions;

use Vis\Builder\Http\Services\Actions;
use App\Models\modelName;
use Vis\Builder\Http\Fields\{Datetime, Id, Text, Checkbox, Textarea};
use Vis\Builder\Http\Definitions\Resource;

class modelPluralName extends Resource
{
    public string $model = modelName::class;
    public string $title = 'modelName';
    protected string $orderBy = 'priority asc';
    protected bool $isSortable = true;

    public function fields(): array
    {
        return [
            'test' => [
                Id::make('#', 'id')->sortable(),
                fieldsDescription,
                Datetime::make('Дата создания', 'created_at')->filter()->sortable(),
            ],
        ];
    }


    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->preview()->clone()->delete();
    }

}
