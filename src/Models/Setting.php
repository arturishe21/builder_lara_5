<?php

namespace Vis\Builder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Venturecraft\Revisionable\RevisionableTrait;
use Vis\Builder\Helpers\Traits\Rememberable;
use Illuminate\Support\Str;

class Setting extends Model
{
    use RevisionableTrait, Rememberable;

    protected $fillable = [
        'type',
        'title',
        'slug',
        'value',
        'group_type',
    ];

    public $timestamps = false;

    public static function get($slug, $default = '', $useLocale = false)
    {
        $langThis = $useLocale ? app()->getLocale() : defaultLanguage();
        $cacheKey = "settings:$slug:".$langThis;

        if (Cache::tags('settings')->has($cacheKey)) {
            return Cache::tags('settings')->get($cacheKey);
        }

        $setting = self::where('slug', 'like', $slug)->first();

        if ($setting) {

            $valueJson = json_decode($setting->value);
            $value = $valueJson->{$langThis} ?? '';

            Cache::tags('settings')->forever($cacheKey, $value);

            return $value;
        }

        return $default;
    }

    public static function getWithLang($slug, $default = '')
    {
        return self::get($slug, $default, true);
    }

    public function doSave($data, $file)
    {
        if ($data['id'] == 0) {
            $settings = new Setting();
        } else {
            $settings = Setting::find($data['id']);
        }

        $settings->title = $data['title'];
        $settings->slug = $data['slug'];
        $settings->type = $data['type'];
        $settings->group_type = $data['group'];

        if ($data['type'] < 2 || $data['type'] == 6) {
            $settings->value = $data['value'.$data['type']];
        }

        //yes/no
        if ($data['type'] == 7) {
            $settings->value = $data['status'];
        }

        //if type file
        if ($data['type'] == 4 && $file) {
            $destinationPath = 'storage/settings';
            $ext = $file->getClientOriginalExtension();

            $nameFile = Str::slug(rtrim($file->getClientOriginalName(), $ext));

            $nameFile = $nameFile.'.'.$ext;
            $fullPathImg = '/'.$destinationPath.'/'.$nameFile;
            $file->move($destinationPath, $nameFile);
            $settings->value = $fullPathImg;
        }

        if (is_array(config('builder.settings.langs')) && ($data['type'] < 2 || $data['type'] == 6)) {
            foreach (config('builder.settings.langs') as $prefix => $value) {
                $field = 'value'.$prefix;

                if (isset($data['value'.$data['type'].$prefix])) {
                    $settings->$field = $data['value'.$data['type'].$prefix];
                }
            }
        }

        $settings->save();

        $this->clearCache();

        return $settings;
    }

    public function clearCache()
    {
        Cache::tags('settings')->flush();
    }

}
