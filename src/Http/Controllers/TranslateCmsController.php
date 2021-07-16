<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Vis\Builder\Models\{TranslationsPhrasesCms, TranslationsCms};
use Vis\Builder\Libs\GoogleTranslateForFree;

/**
 * Class TranslateController.
 */
class TranslateCmsController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $search = request('search_q');
        $countShow = request('count_show') ? request('count_show') : '20';

        $allpage = TranslationsPhrasesCms::orderBy('id', 'desc');

        if ($search) {
            $allpage = $allpage->where('phrase', 'LIKE', '%'.$search.'%');
        }

        $allpage = $allpage->paginate($countShow);

        $breadcrumb[__cms('Переводы CMS')] = '';

        $view = Request::ajax() ? 'admin::translation_cms.part.center' : 'admin::translation_cms.trans';

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
    public function create()
    {
        $langs = config('builder.translations.cms.languages');

        return view('admin::translation_cms.part.form', compact('langs'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTraslate()
    {
        $validator = Validator::make(request()->all(), TranslationsPhrasesCms::$rules);
        if ($validator->fails()) {
            return Response::json(
                [
                    'status' => 'error',
                    'message' => $validator->messages(),
                ]
            );
        }

        $model = new TranslationsPhrasesCms();
        $model->phrase = trim(request()->get('phrase'));
        $model->save();

        $langs = array_keys(config('builder.translations.cms.languages'));

        foreach (request()->get('translation') as $slugTranslate => $translate) {
                $model_trans = new TranslationsCms();
                $model_trans->translate = trim($translate);
                $model_trans->lang = $slugTranslate;
                $model_trans->translations_phrases_cms_id = $model->id;
                $model_trans->save();
        }

        TranslationsPhrasesCms::reCacheTrans();

        return Response::json(
            [
                'status'      => 'success',
                'message' => __cms('Фраза успешно добавлена'),
            ]
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        TranslationsPhrasesCms::find($id)->delete();
        TranslationsPhrasesCms::reCacheTrans();

        return Response::json(['status' => 'ok']);
    }

    /**
     * @return false|string
     */
    public function doTranslate(GoogleTranslateForFree $googleTranslateForFree)
    {
        $language = request('language') == 'ua' ? 'uk' : request('language');

        return Response::json([
            'text' => $googleTranslateForFree->translate('ru', $language, request('phrase'), 2)
        ]);
    }

    public function changeTranslate()
    {
        $lang = request('name');
        $value = request('value');
        $id = request('pk');

        $phrase = TranslationsCms::where('translations_phrases_cms_id', $id)->where('lang', $lang)->first();

        $phrase->translate = $value;
        $phrase->save();

        TranslationsPhrasesCms::reCacheTrans();
    }
}
