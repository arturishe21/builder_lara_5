<?php

namespace App\Cms\Tree\Templates;

use Vis\Builder\Fields\{
    Id,
    Text
};
use Vis\Builder\Definitions\ResourceTree;

class Contacts extends ResourceTree
{
    protected $titleDefinition = 'Контакты';
    protected $action = 'ContactsController@showPage';

    public function fields()
    {
        return [
            'test' => [
                Id::make('#', 'id')->sortable(),

                Text::make('11', 'slug')
                    ->comment('sdsds')
                    ->sortable(),

            ],
        ];
    }
}
