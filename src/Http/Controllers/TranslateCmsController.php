<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Vis\Builder\Http\Requests\TranslateCms;
use Vis\Builder\Models\{TranslationsPhrasesCms, TranslationsCms};
use Vis\Builder\Libs\GoogleTranslateForFree;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class TranslateCmsController extends Controller
{
    private int $countShow;
    private array $languages;

    public function __construct()
    {
        $this->countShow = request('count_show', 20);
        $this->languages = config('builder.translations.cms.languages');
    }

    public function index(): View
    {
        $search = request('search_q');
        $phrases = TranslationsPhrasesCms::orderBy('id', 'desc');

        if ($search) {
            $phrases = $phrases->where('phrase', 'LIKE', '%'.$search.'%');
        }

        $phrases = $phrases->paginate($this->countShow);
        $view = Request::ajax() ? 'admin::translation_cms.part.center' : 'admin::translation_cms.trans';

        return view($view)->with(
            [
            'phrases' => $phrases,
            'langs' => $this->languages,
            'search_q' => $search,
            'count_show' => $this->countShow
            ]
        );
    }

    public function create(): View
    {
        $langs = $this->lanuages;

        return view('admin::translation_cms.part.form', compact('langs'));
    }

    public function saveTranslate(TranslateCms $request): JsonResponse
    {
        $translations = [];
        $model = TranslationsPhrasesCms::create(
            [
            'phrase' => $request->get('phrase')
            ]
        );

        foreach (request()->get('translation') as $slugTranslate => $translate) {
            $translations[] = new TranslationsCms(
                [
                'translate' => trim($translate),
                'lang' => $slugTranslate,
                ]
            );
        }

        $model->translations()->saveMany($translations);

        TranslationsPhrasesCms::reCacheTrans();

        return response()->json(
            [
                'status'      => 'success',
                'message' => __cms('Фраза успешно добавлена'),
            ]
        );
    }

    public function destroy($id): JsonResponse
    {
        TranslationsPhrasesCms::find($id)->delete();
        TranslationsPhrasesCms::reCacheTrans();

        return response()->json(['status' => 'ok']);
    }

    public function doTranslate(GoogleTranslateForFree $googleTranslateForFree): JsonResponse
    {
        $language = request('language') === 'ua' ? 'uk' : request('language');

        return response()->json(
            [
            'text' => $googleTranslateForFree->translate('ru', $language, request('phrase'), 2)
            ]
        );
    }

    public function changeTranslate(): void
    {
        $phrase = TranslationsCms::query()
            ->where('translations_phrases_cms_id', request()->get('pk'))
            ->where('lang', request()->get('name'))
            ->first();

        $phrase->update(
            [
            'translate' => request()->get('value')
            ]
        );

        TranslationsPhrasesCms::reCacheTrans();
    }
}
