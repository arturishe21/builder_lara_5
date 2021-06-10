<?php

namespace Vis\Builder\Fields;

use App\Cms\Admin;

class Permissions extends Field
{
    private $actions = [
        'view' => 'Просмотр',
        'insert' => 'Создание',
        'update' => 'Редактирование',
        'save' => 'Сохранение',
        'clone' => 'Клонирование',
        'revisions' => 'История',
        'delete' => 'Удаление'
    ];

    public function getFieldForm($definition)
    {
        $permissions = $this->generatePermissions();
        $groupPermissionsThis = $this->getValue();

        return view('admin::form.fields.permissions', compact('permissions', 'groupPermissionsThis'))->render();
    }

    public function prepareSave($request)
    {
        $nameField = $this->getNameField();

        return array_map('boolval', $request[$nameField]);
    }

    private function generatePermissions()
    {
        $permissionsMenu = (new Admin())->menu();

        $permissions['Дооступ в cms'] = [
            "admin.access" => "Да"
        ];


        foreach ($permissionsMenu as $permission) {
            if (isset($permission['link']) && isset($permission['title'])) {
                $slug = $this->prepareSlug($permission['link']);

                foreach ($this->actions as $slugAction => $titleAction) {
                    $permissions[$permission['title']][$slug.'.'. $slugAction] = $titleAction;
                }
            }

            if (isset($permission['submenu'])) {
                foreach ($permission['submenu'] as $subMenu) {
                    if (isset($subMenu['link'])) {
                        $slug = $this->prepareSlug($subMenu['link']);
                        if (isset($subMenu['link']) && isset($subMenu['title'])) {
                            $permissions[$permission['title']][$subMenu['title']][$slug.'.view'] = 'Просмотр';

                            foreach ($this->actions as $slugAction => $titleAction) {
                                $permissions[$permission['title']][$subMenu['title']][$slug.'.'.$slugAction] = $titleAction;
                            }

                            if (isset($subMenu['submenu'])) {
                                foreach ($subMenu['submenu'] as $subMenuLevel2) {
                                    $slug = $this->prepareSlug($subMenuLevel2['link']);

                                    foreach ($this->actions as $slugAction => $titleAction) {
                                        $permissions[$permission['title']][$subMenu['title']][$subMenuLevel2['title']][$slug.'.'. $slugAction] = $titleAction;
                                    }

                                }
                            }

                        }
                    }
                }
            }
        }

        return $permissions;
    }

    private function prepareSlug($link)
    {
        return str_replace(['/'], [''], $link);
    }
}
