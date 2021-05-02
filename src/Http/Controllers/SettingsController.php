<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

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

        $view = Request::ajax() ? 'settings.part.center' : 'settings.settings_all';

        return view('admin::'.$view, compact('title', 'breadcrumb', 'data', 'groups'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin::settings.part.form');
    }

    /**
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function update(Setting $setting)
    {
        $file = request()->file('file');
        parse_str(request('data'), $data);

        $setting->doSave($data, $file);

        return Response::json(
            [
                'status'            => 'ok',
                'ok_messages'       => __cms('Сохренено'),
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Setting $setting)
    {
        $info = $setting;

        return view('admin::settings.part.form', compact('info'));
    }

    /**
     * quick edit in list.
     */
    public function fastSave(Setting $setting)
    {
        $setting->update([
            'value' => request('value')
        ]);

        $setting->clearCache();
    }
}
