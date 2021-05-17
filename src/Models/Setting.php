<?php

namespace Vis\Builder;

use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Helpers\Traits\TranslateTrait;

class Setting extends Model
{
    use TranslateTrait;

    protected $table = 'settings';
    protected $fillable = [];
    public $timestamps = false;

    public function getValue(string $slug)
    {
        $setting = $this->whereSlug($slug)->first();

        if ($setting) {
            switch ($setting->type) {
                case 'text':
                    return $setting->value;
                case 'text_with_languages':
                    return $setting->t('value_languages');
                case 'textarea_with_languages':
                    return $setting->t('textarea_with_languages');
                case 'froala_with_languages':
                    return $setting->t('froala_with_languages');
                case 'file':
                    return $setting->file;
                case 'check':
                    return $setting->check;
            }
        }
    }
}
