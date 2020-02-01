<?php

namespace Vis\TranslationsCMS;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class TranslateController.
 */
class TranslateController extends Controller
{
    /**
     * @return mixed
     */
    public function fetchIndex()
    {
        $search = request('search_q');
        $countShow = request('count_show') ? request('count_show') : '20';

        $allpage = Trans::orderBy('id', 'desc');

        if ($search) {
            $allpage = $allpage->where('phrase', 'LIKE', '%'.$search.'%');
        }

        $allpage = $allpage->paginate($countShow);

        $breadcrumb[__cms('Переводы CMS')] = '';

        $view = Request::ajax() ? 'admin::translation_cms.part.translate_cms_center' : 'admin::translation_cms.trans';

        $langs = config('builder.translations.cms.languages');

        return view($view)
            ->with('breadcrumb', $breadcrumb)
            ->with('data', $allpage)
            ->with('langs', $langs)
            ->with('search_q', $search)
            ->with('count_show', $countShow);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetchCreate()
    {
        $langs = config('builder.translations.cms.languages');

        return view('admin::translation_cms.part.form_trans')->with('langs', $langs);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doSaveTranslate()
    {
        parse_str(request('data'), $data);

        $validator = Validator::make($data, Trans::$rules);
        if ($validator->fails()) {
            return Response::json(
                [
                    'status'          => 'error',
                    'errors_messages' => $validator->messages(),
                ]
            );
        }

        $model = new Trans();
        $model->phrase = trim($data['phrase']);
        $model->save();

        $langs = array_keys(config('builder.translations.cms.languages'));

        foreach ($data as $k => $el) {
            if (in_array($k, $langs) && $el && $model->id) {
                $model_trans = new  Translate();
                $model_trans->translate = trim($el);
                $model_trans->lang = $k;
                $model_trans->translations_phrases_cms_id = $model->id;
                $model_trans->save();
            }
        }

        Trans::reCacheTrans();

        return Response::json(
            [
                'status'      => 'ok',
                'ok_messages' => __cms('Фраза успешно добавлена'),
            ]
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doDelelePhrase()
    {
        Trans::find(request('id'))->delete();

        Trans::reCacheTrans();

        return Response::json(['status' => 'ok']);
    }

    /**
     * @return false|string
     */
    public function doTranslate(Translate $trans)
    {
       return $trans->generateTranslate(request('lang'), request('phrase'));
    }

    public function doSavePhrase()
    {
        $lang = request('name');
        $phrase = request('value');
        $id = request('pk');
        if ($id && $phrase && $lang) {
            $phrase_change = Translate::where('translations_phrases_cms_id', $id)->where('lang', $lang)->first();
            $phrase_change->translate = $phrase;
            $phrase_change->save();
        }
        Trans::reCacheTrans();
    }
}
