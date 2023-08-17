<?php

namespace App\Cms\Tree\Templates;

use Vis\Builder\Http\Fields\{Checkbox, Froala, Id, Image, MultiImage, Text, Textarea};

use Vis\Builder\Http\Definitions\ResourceTree;

class Contacts extends ResourceTree
{
    protected string $titleDefinition = 'Контакты';
    protected string $action = 'ContactsController@showPage';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title'),
                Froala::make('Описание', 'description'),
                Text::make('Url', 'slug'),
                Image::make('Картинка', 'picture'),
                MultiImage::make('Дополнительные картинки', 'additional_pictures'),
                Checkbox::make('Активно' ,'is_active'),
            ],
            'SEO' => [
                Text::make('Seo title', 'seo_title'),
                Textarea::make('Seo description', 'seo_description')
            ]

        ];
    }
}
