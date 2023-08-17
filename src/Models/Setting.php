<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Http\Traits\TranslateTrait;

class Setting extends Model
{
    use TranslateTrait;

    protected $table = 'settings';
    public $timestamps = false;

    public function getValue(string $slug)
    {
        $setting = $this->whereSlug($slug)->first();

        if ($setting) {
            return $this->getResultType($setting)[$setting->type] ?? '';
        }
    }

    private function getResultType($setting): array
    {
        return [
            'text' => $setting->value,
            'text_with_languages' => $setting->t('value_languages'),
            'textarea_with_languages' => $setting->t('textarea_with_languages'),
            'froala_with_languages' => $setting->t('froala_with_languages'),
            'file' => $setting->file,
            'checkbox' => $setting->check
        ];
    }
}
