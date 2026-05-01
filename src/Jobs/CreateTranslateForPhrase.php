<?php

namespace Vis\Builder\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vis\Builder\Libs\GoogleTranslateForFree;
use Vis\Builder\Models\TranslationsPhrase;

class CreateTranslateForPhrase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private string $phrase)
    {
    }

    public function handle(): void
    {
        $checkPresentPhrase = TranslationsPhrase::where('phrase', 'like', $this->phrase)->first();

        if ($checkPresentPhrase) {
            return;
        }

        $newPhrase = TranslationsPhrase::create(['phrase' => $this->phrase]);

        $defaultLanguage = defaultLanguage();
        $languages = languagesOfSite();

        foreach ($languages as $language) {

            try {
                $translate = GoogleTranslateForFree::translate($defaultLanguage, $language, $this->phrase, 2);
            } catch (\Exception $e) {
                $translate = $this->phrase;
            }

            $newPhrase->translations()->create([
                'lang' => $language,
                'translate' => $translate,
            ]);
        }

        TranslationsPhrase::reCacheTrans();
    }
}
