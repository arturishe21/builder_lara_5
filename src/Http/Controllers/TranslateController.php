<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Vis\Builder\Http\Requests\Translate;
use Vis\Builder\Models\Translations;
use Vis\Builder\Models\TranslationsPhrases;

class TranslateController extends Controller
{
    private $countRecordsOnPage = 20;

    public function index()
    {
        if (request('search_q') && mb_strlen(request('search_q')) > 1) {
            return $this->search();
        }

        $allPhrases = TranslationsPhrases::orderBy('id', 'desc')->paginate($this->countRecordsOnPage);

        $view = Request::ajax() ? 'admin::translations.part.table_center' : 'admin::translations.trans';

        return view($view, compact('allPhrases'));
    }

    /**
     * search in list phrase.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function search()
    {
        $querySearch = trim(request('search_q'));

        $allPhrases = TranslationsPhrases::leftJoin('translations', 'translations.id_translations_phrase', '=', 'translations_phrases.id')
            ->select('translations_phrases.*')
            ->where(function ($query) use ($querySearch) {
                $query->where('phrase', 'like', '%'.$querySearch.'%')
                    ->orWhere('translations.translate', 'like', '%'.$querySearch.'%');
            })
            ->groupBy('translations_phrases.id')
            ->orderBy('translations_phrases.id', 'desc')->paginate($this->countRecordsOnPage);

        return view('admin::translations.part.result_search', compact('allPhrases'));
    }

    /**
     * get popup for create new phrase.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function shopPopupForCreate()
    {
        return view('admin::translations.part.form_trans');
    }

    /**
     * create new translation.
     *
     * @return json Response
     */
    public function saveTranslate(Translate $request)
    {
        $model = TranslationsPhrases::create([
            'phrase' => strip_tags(str_replace('"', '', trim($request->get('phrase'))))
        ]);

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
    public function remove()
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
    public function savePhrase()
    {
        $lang = request('name');
        $phrase = request('value');
        $id = request('pk');

        if ($id && $phrase && $lang) {

            $phraseChange = Translations::where('id_translations_phrase', $id)->where('lang', $lang)->first();

            if ($phraseChange) {
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
