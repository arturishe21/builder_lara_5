<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Vis\Builder\Http\Requests\TranslateCms;
use Vis\Builder\Models\{TranslationsPhrasesCms, TranslationsCms};
use Vis\Builder\Libs\GoogleTranslateForFree;

/**
 * Class TranslateController.
 */
class TranslateCmsController extends Controller
{
    private $countShow;
    private $lanuages;

    public function __construct()
    {
        $this->countShow = request('count_show') ? : '20';
        $this->lanuages = config('builder.translations.cms.languages');
    }

    public function index()
    {
        $search = request('search_q');
        $phrases = TranslationsPhrasesCms::orderBy('id', 'desc');

        if ($search) {
            $phrases = $phrases->where('phrase', 'LIKE', '%'.$search.'%');
        }

        $phrases = $phrases->paginate($this->countShow);
        $view = Request::ajax() ? 'admin::translation_cms.part.center' : 'admin::translation_cms.trans';

        return view($view)
            ->with('phrases', $phrases)
            ->with('langs', $this->lanuages)
            ->with('search_q', $search)
            ->with('count_show', $this->countShow);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $langs = $this->lanuages;

        return view('admin::translation_cms.part.form', compact('langs'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTranslate(TranslateCms $request)
    {
        $translations = [];
        $model = TranslationsPhrasesCms::create([
            'phrase' => $request->get('phrase')
        ]);

        foreach (request()->get('translation') as $slugTranslate => $translate) {
            $translations[] = new TranslationsCms([
                'translate' => trim($translate),
                'lang' => $slugTranslate,
            ]);
        }

        $model->translations()->saveMany($translations);

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

        $phrase->update([
            'translate' => $value
        ]);

        TranslationsPhrasesCms::reCacheTrans();
    }
}
