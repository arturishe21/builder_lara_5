<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

/**
 * Class SettingsController.
 */
class SettingsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function fetchIndex()
    {
        $breadcrumb[config('builder.settings.title_page')] = '';
        $groupsSettings = config('builder.settings.groups');

        $data = Setting::orderBy('id', 'desc');
        $title = config('builder.settings.title_page');

        //filter group
        if (request('group')) {
            $data = $data->where('group_type', request('group'));
            $title .= ' / '.$groupsSettings[request('group')];
        }

        $data = $data->paginate(20);
        $groups = config('builder.settings.groups');

        $view = Request::ajax() ? 'settings.part.settings_center' : 'settings.settings_all';

        return view('admin::'.$view, compact('title', 'breadcrumb', 'data', 'groups'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function fetchCreate()
    {
        $type = config('builder.settings.type');
        $groups = config('builder.settings.groups');

        return view('admin::settings.part.form_settings', compact('type', 'groups'));
    }

    /**
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function doSave()
    {
        $file = request()->file('file');
        parse_str(request('data'), $data);

        $validation = Setting::isValid($data, $data['id']);

        if ($validation) {
            return $validation;
        }

        Setting::doSaveSetting($data, $file);

        $message = $data['id'] != 0 && is_numeric($data['id']) ? 'Запись успешно обновлена' : 'Запись успешно добавлена';

        return Response::json(
            [
                'status'            => 'ok',
                'ok_messages'       => $message,
            ]
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doDelete()
    {
        Setting::doDelete(request('id'));

        return Response::json(
            [
                'status' => 'ok',
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function fetchEdit()
    {
        $id = request('id');

        $info = Setting::findOrFail($id);
        $type = config('builder.settings.type');
        $groups = config('builder.settings.groups');

        $select_info = [];
        if ($info->type == 2 || $info->type == 3 || $info->type == 5) {
            $select_info = SettingSelect::where('id_setting', $info->id)
                ->orderBy('priority')
                ->get()
                ->toArray();
        }

        return view('admin::settings.part.form_settings',
            compact('info', 'type', 'select_info', 'groups'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doDeleteSettingSelect()
    {
        SettingSelect::doDelete(request('id'));

        return Response::json(
            [
                'status' => 'ok',
                'text'   => 'Запись успешно удалена',
            ]
        );
    }

    /**
     * quick edit in list.
     */
    public function doFastSave()
    {
        if (request('id')) {
            $setting = Setting::find(request('id'));
            $setting->value = trim(request('value'));
            $setting->save();

            Setting::reCacheSettings();
        }
    }
}
