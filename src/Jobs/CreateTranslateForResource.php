<?php

namespace Vis\Builder\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vis\Builder\Http\Definitions\Resource;
use Vis\Builder\Libs\GoogleTranslateForFree;

class CreateTranslateForResource implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private Model $model, private Resource $resource, private array $fields)
    {
    }

    public function handle(): void
    {
        $defaultLanguage = defaultLanguage();

        foreach ($this->fields as $nameField => $field) {
            $textForTranslate = $field[$defaultLanguage] ?? '';

            foreach ($field as $lang => $value) {
                if (empty($value)) {
                    try {
                        $translate = GoogleTranslateForFree::translate($defaultLanguage, $lang, $textForTranslate, 2);
                    } catch (\Exception $e) {
                        $translate = $textForTranslate;
                    }

                    $field[$lang] = $translate;
                }
            }

            $this->model->{$nameField} = json_encode($field);
        }

        $this->model->save();
        $this->resource->clearCache();
    }
}
