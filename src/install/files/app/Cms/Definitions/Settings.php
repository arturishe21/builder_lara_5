<?php

namespace App\Cms\Definitions;

use Vis\Builder\Fields\{Checkbox, File, ForeignAjax, Froala, Hidden, Id, Select, Text, Relations\Options, Textarea};
use Vis\Builder\Definitions\Resource;
use Illuminate\Validation\Rule;
use Vis\Builder\Fields\Custom\TextSetting;

class Settings extends Resource
{
    public $model = \Vis\Builder\Setting::class;
    public $title = 'Настройки';
    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('group', 'group')->default('general'),
            Text::make('Заголовок', 'title')->filter()->sortable()
                ->transliteration('slug', true),
            Text::make('Код для вставки', 'slug')->filter()->sortable()
                ->rules([
                    'required',
                    Rule::unique('settings')->ignore(request('id')),
                ])
            ,
            Select::make('Тип', 'type')
                ->options([
                    'text' => 'Текст',
                    'text_with_languages' => 'Текст с переводами',
                    'textarea_with_languages' => 'Большой текст с переводами',
                    'froala_with_languages' => 'Текстовый редактор с переводами',
                    'file' => 'Файл',
                    'checkbox' => 'Вкл/Выкл',
                ])->action(),

            TextSetting::make('Значение', 'value')
                ->className('text'),

            Text::make('Значение', 'value_languages')->language()->onlyForm()
                ->className('text_with_languages'),

            Textarea::make('Значение', 'textarea_with_languages')->language()->onlyForm()
                ->className('textarea_with_languages'),

            Froala::make('Значение', 'froala_with_languages')->language()->onlyForm()
                ->className('froala_with_languages'),

            File::make('Значение', 'file')->onlyForm()
                ->className('file'),

            Checkbox::make('Значение', 'check')->onlyForm()
                ->className('checkbox'),
        ];
    }
}
