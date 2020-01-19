<?php

namespace App\Cms\Tree\Templates;

use Vis\Builder\FieldsNew\{
    Id,
    Text
};

use Vis\Builder\Definitions\ResourceTree;

class Node extends ResourceTree
{
    protected $titleDefinition = 'Главный';
    public $action = 'ContactsController@showPage';

    public function fields()
    {
        return [
            'test' => [
                Id::make('#', 'id')->sortable(),

                Text::make('Заголовок', 'title')
                    ->comment('sdsds')
                    ->sortable(),

            ],
            'test2' => [
            ]

        ];
    }
}
