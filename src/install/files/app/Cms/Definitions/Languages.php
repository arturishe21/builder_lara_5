<?php

namespace App\Cms\Definitions;

use Vis\Builder\Http\Services\Actions;
use Vis\Builder\Models\Language;
use Vis\Builder\Http\Fields\{Checkbox, Select};
use Vis\Builder\Http\Definitions\Resource;

class Languages extends Resource
{
    public string $model = Language::class;
    public string $title = 'Языки сайта';
    protected string $orderBy = 'priority asc';
    protected bool $isSortable = true;

    public function fields(): array
    {
        return [
            Select::make('Язык', 'language')
                ->options($this->model()->supportedLocales()),
            Checkbox::make('Активен', 'is_active')->fastEdit(),
        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->hideActions();
    }

}
