<?php

namespace App\Cms\Definitions;

use Vis\Builder\Http\Services\Actions;
use App\Models\Article;
use Vis\Builder\Http\Fields\{Color,
    Hidden,
    ManyToManyAjax,
    MultiImage,
    Relations\Options,
    Select,
    Password,
    ForeignAjax,
    Id,
    Checkbox,
    Datetime,
    Image,
    File,
    Text,
    Definition};
use Vis\Builder\Http\Definitions\Resource;

class Articles extends Resource
{
    public string $model = Article::class;
    public string $title = 'Статьи';
    protected string $orderBy = 'priority asc';
    protected bool $isSortable = true;

    public function fields()
    {
        return [
            'test' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Картинки111', 'title'),
                Image::make('Картинки111', 'picture'),
                Checkbox::make('Checkbox', 'checkbox')->filter(),
                Datetime::make('Datetime', 'created_at')->filter()->sortable(),
                ManyToManyAjax::make('Статьи')
                    ->options(
                        (new Options('trees'))
                            ->where('parent_id', '=', '1')
                            ->orderBy('created_at', 'asc')
                    ),
                ForeignAjax::make('Дерево', 'tree_id')
                    ->filter()
                    ->options(
                        (new Options('trees2'))
                            ->where('parent_id', '=', '1')
                            ->orderBy('created_at', 'asc')
                    ),
              //  Definition::make('Новости')->hasMany('news', News::class)
            ],
            'test2' => [
            ]

        ];
    }
    

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->preview()->delete()->clone();
    }

}
