<?php

namespace App\Cms\Definitions;

use Vis\Builder\Services\Actions;
use App\Models\modelName;
use Vis\Builder\Fields\{Datetime, Id, Text, Checkbox, Textarea};
use Vis\Builder\Definitions\Resource;

class modelPluralName extends Resource
{
    public $model = modelName::class;
    public $title = 'modelName';
    protected $orderBy = 'priority asc';
    protected $isSortable = true;

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
