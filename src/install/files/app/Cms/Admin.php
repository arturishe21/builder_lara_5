<?php

namespace App\Cms;

use Vis\Builder\Setting\AdminBase;

class Admin extends AdminBase
{
    public function menu()
    {
        return [

            [
                'title' => 'Структура сайта',
                'icon'  => 'sitemap',
                'link'  => '/tree',
                'check' => function () {
                    return true;
                },
            ],

            [
                'title' => 'Статьи',
                'icon'  => 'building',
                'link'  => '/articles',
                'check' => function () {
                    return true;
                },
            ],

            [
                'title' => 'Настройки',
                'icon'  => 'cog',
                'badge' => function() {   //лейбла с количеством чего-то
                    return \App\Models\Article::all()->count();
                },
                'submenu' => [
                    [
                        'title' => 'Управление',
                        'submenu' => [
                            [
                                'title' => 'Общее',
                                'link'  => '/settings/settings_all?group=general',
                                'check' => function () {
                                    return true;
                                },


                            ],
                        ],
                        'check' => function () {
                            return true;
                        },
                    ],
                    [
                        'title' => 'Переводы CMS',
                        'link'  => '/translations_cms/phrases',
                        'check' => function () {
                            return true;
                        },
                    ],
                    [
                        'title' => 'Контроль изменений',
                        'link'  => '/revisions',
                        'check' => function () {
                            return true;
                        },
                    ],
                ],
            ],

            array(
                'title' => 'Переводы',
                'icon'  => 'language',
                'link'  => '/translations/phrases',
                'check' => function() {
                    return true;
                }
            ),

            [
                'title' => 'Упр. пользователями',
                'icon'  => 'user',
                'submenu' => [
                    [
                        'title' => 'Пользователи',
                        'link'  => '/users',
                        'check' => function () {
                            return true;
                        },
                    ],

                    [
                        'title' => 'Группы',
                        'link'  => '/groups',
                        'check' => function () {
                            return true;
                        },
                    ],

                ],
            ],
        ];
    }

}
