<?php

namespace App\Cms\Tree\Templates;

use Vis\Builder\Fields\{Checkbox, Froala, Id, Image, MultiImage, Text};
use Vis\Builder\Definitions\ResourceTree;
use App\Models\MorphOne\Seo;

class Node extends ResourceTree
{
    protected $titleDefinition = 'Главный';
    public $action = 'HomeController@index';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title')->language(),
                Froala::make('Описание', 'description')->language(),
                Text::make('Url', 'slug'),
                Image::make('Картинка', 'picture'),
                MultiImage::make('Дополнительные картинки', 'additional_pictures'),
                Checkbox::make('Активно' ,'is_active'),
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
