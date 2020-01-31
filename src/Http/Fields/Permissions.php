<?php

namespace Vis\Builder\Fields;

use App\Cms\Admin;

class Permissions extends Field
{
    public function getFieldForm($definition)
    {
        $permissions = $this->generatePermissions();
        $groupPermissionsThis = $this->getValue();

        return view('admin::new.form.fields.permissions', compact('permissions', 'groupPermissionsThis'))->render();
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
                $slug = str_replace('/', '', $permission['link']);

                $actions = config('builder.tb-definitions.'.$slug.'.actions');

                if (is_array($actions)) {
                    $permissions[$permission['title']][$slug.'.view'] = 'Просмотр';
                    foreach ($actions as $slugAction => $action) {
                        if (isset($action['caption'])) {
                            $permissions[$permission['title']][$slug.'.'.$slugAction] = $action['caption'];
                        }
                    }
                } else {
                    $actions = config('builder.'.$slug.'.actions');

                    if (is_array($actions)) {
                        $permissions[$permission['title']][$slug.'.view'] = 'Просмотр';
                        foreach ($actions as $slugAction => $action) {
                            if (isset($action['caption'])) {
                                $permissions[$permission['title']][$slug.'.'.$slugAction] = $action['caption'];
                            }
                        }
                    } else {
                        $permissions[$permission['title']][$slug.'.view'] = 'Просмотр';
                    }
                }
            }

            if (isset($permission['submenu'])) {
                foreach ($permission['submenu'] as $subMenu) {
                    if (isset($subMenu['link'])) {
                        $slug = str_replace('/', '', $subMenu['link']);
                        $actions = config('builder.tb-definitions.'.$slug.'.actions');

                        if (isset($subMenu['link']) && isset($subMenu['title'])) {
                            $permissions[$permission['title']][$subMenu['title']][$slug.'.view'] = 'Просмотр';

                            if (is_array($actions)) {
                                foreach ($actions as $slugAction => $action) {
                                    $permissions[$permission['title']][$subMenu['title']][$slug.'.'.$slugAction]
                                        = $action['caption'];
                                }
                            }
                        }
                    }
                }
            }

        }

        return $permissions;
    }
}
