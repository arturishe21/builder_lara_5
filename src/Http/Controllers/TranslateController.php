<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Vis\Builder\Models\Language;
use Vis\Builder\Models\Translations;
use Vis\Builder\Models\TranslationsPhrases;

class TranslateController extends Controller
{
    private $languages;

    public function __construct(Language $language)
    {
        $this->languages = $language->getLanguages();
    }

    public function fetchIndex()
    {
        if (request('search_q') && mb_strlen(request('search_q')) > 1) {
            return $this->doSearch();
        }

        $countShow = request('count_show') ?: 20;
        $allpage = TranslationsPhrases::orderBy('id', 'desc')->paginate($countShow);

        $view = Request::ajax() ? 'admin::translations.part.table_center' : 'admin::translations.trans';
        $languages = $this->languages;

        return view($view)
            ->with('allPage', $allpage)
            ->with('languages', $languages)
            ->with('count_show', $countShow);
    }

    /**
     * do search in list phrase.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function doSearch()
    {
        $querySearch = trim(request('search_q'));
        $languages = $this->languages;

        $allPage = TranslationsPhrases::leftJoin('translations', 'translations.id_translations_phrase', '=', 'translations_phrases.id')
            ->select('translations_phrases.*')
            ->where(function ($query) use ($querySearch) {
                $query->where('phrase', 'like', '%'.$querySearch.'%')
                    ->orWhere('translations.translate', 'like', '%'.$querySearch.'%');
            })

            ->groupBy('translations_phrases.id')
            ->orderBy('translations_phrases.id', 'desc')->paginate(20);

        return view('admin::translations.part.result_search', compact('allPage', 'languages'));
    }

    /**
     * get popup create new phrase.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function fetchCreate()
    {
        $languages = $this->languages;

        return view('admin::translations.part.form_trans', compact('languages'));
    }

    /**
     * do create new translation.
     *
     * @return json Response
     */
    public function doSaveTranslate()
    {

        $validator = Validator::make(request()->all(), TranslationsPhrases::$rules);
        if ($validator->fails()) {
            return Response::json(
                [
                    'status' => 'error',
                    'message' => $validator->messages(),
                ]
            );
        }

        $model = new TranslationsPhrases();
        $model->phrase = strip_tags(str_replace('"', '', trim(request()->get('phrase'))));
        $model->save();

        foreach (request()->get('translation') as $slugTranslate => $translate) {
                Translations::create([
                    'id_translations_phrase' => $model->id,
                    'lang' => $slugTranslate,
                    'translate' => trim($translate),
                ]);
        }

        TranslationsPhrases::reCacheTrans();

        return Response::json(
            [
                'status'      => 'success',
                'message' => __cms('Фраза успешно добавлена'),
            ]
        );
    }

    /**
     * delete phrase.
     *
     * @return json Response
     */
    public function doDelelePhrase()
    {
        TranslationsPhrases::find(request('id'))->delete();

        TranslationsPhrases::reCacheTrans();

        return Response::json(['status' => 'ok']);
    }

    /**
     * save phrase.
     *
     * @return void
     */
    public function doSavePhrase()
    {
        $lang = request('name');
        $phrase = request('value');
        $id = request('pk');

        if ($id && $phrase && $lang) {
            $phraseChange = Translations::where('id_translations_phrase', $id)->where('lang', $lang)->first();
            if (isset($phraseChange->id)) {
                $phraseChange->translate = $phrase;
                $phraseChange->save();
            } else {
                Translations::create(
                    [
                        'id_translations_phrase' => $id,
                        'lang'                   => $lang,
                        'translate'              => $phrase,
                    ]
                );
            }
        }

        TranslationsPhrases::reCacheTrans();
    }

    public function getJs($lang, $withoutHeader = false)
    {
        $data = TranslationsPhrases::fillCacheTrans();

        $translates = [];
        foreach ($data as $phrase => $translate) {
            $key = trim(str_replace(["\r\n", "\r", "\n"], '', $phrase));
            $value = trim(isset($translate[$lang]) ? str_replace(["\r\n", "\r", "\n"], '', $translate[$lang]) : '');
            $translates[$key] = $value;
        }

        if ($withoutHeader) {
            return view('admin::translations.js', compact('data', 'lang', 'translates'))->render();
        }

        return response()
            ->view('admin::translations.js', compact('data', 'lang', 'translates'), 200)
            ->header('Content-Type', 'text/javascript');
    }

    public function doTranslatePhraseInJs()
    {
        return __t(request('phrase'));
    }

    public function createdJsFile()
    {
        if (!is_dir(public_path('/js'))) {
            mkdir(public_path('/js'), 0755, true);
        }

        foreach (languagesOfSite() as $lang) {

            $content = $this->getJs($lang, true);

            file_put_contents(public_path('/js/translation_' . $lang . '.js'), $content);
        }
    }
}
