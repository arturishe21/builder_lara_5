<?php

namespace Vis\Builder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Venturecraft\Revisionable\RevisionableTrait;
use Vis\Builder\Helpers\Traits\Rememberable;
use Illuminate\Support\Str;

class Setting extends Model
{
    use RevisionableTrait, Rememberable;

    public static $rules = [
        'title' => 'required',
        'slug'  => 'required|max:256|unique:settings,slug,',
    ];

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
        $cacheKey = "settings:$slug:".app()->getLocale();

        if (Cache::tags('settings')->has($cacheKey)) {
            return Cache::tags('settings')->get($cacheKey);
        }

        $setting = self::where('slug', 'like', $slug)->first();
        $postfix = getLocalePostfix();

        if (! $setting && $default) {
            $defaultColumns = [
                'type'       => 0,
                'title'      => $slug,
                'slug'       => $slug,
                'value'      => $default,
                'group_type' => 'general',
            ];

            if ($useLocale) {
                $defaultColumns["value$postfix"] = $default;
            }

            $setting = self::create($defaultColumns);
        }

        if (isset($setting->id)) {
            $value = $useLocale ? ($setting->{"value$postfix"} ?: $setting->value) : $setting->value;
            $arrayTypes = [2, 3, 5];

            if (in_array($setting->type, $arrayTypes)) {
                $value = $setting->selectValues();
            }

            Cache::tags('settings')->forever($cacheKey, $value);

            return $value;
        }
    }

    public static function getWithLang($slug, $default = '')
    {
        return self::get($slug, $default, true);
    }

    public static function getItem($ids)
    {
        if (! $ids) {
            return [];
        }

        return SettingSelect::find($ids);
    }

    public static function doSaveSetting($data, $file)
    {
        if ($data['id'] == 0) {
            $settings = new self();
        } else {
            $settings = self::find($data['id']);
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

            $nameFile = Str::slug(trim($file->getClientOriginalName(), $ext));

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

        //если тип список
        if ($data['type'] == 2) {
            $i = 0;
            foreach ($data['select'] as $k => $el) {
                $i++;
                if ($el) {
                    if (is_numeric($k)) {
                        $el = trim($el);
                        if ($el) {
                            $SettingSelect = SettingSelect::find($k);
                            $SettingSelect->id_setting = $settings->id;
                            $SettingSelect->value = $el;
                            $SettingSelect->priority = $i;
                            $SettingSelect->save();
                        }
                    } else {
                        foreach ($data['select']['new'] as $el_new) {
                            $el_new = trim($el_new);
                            if ($el_new) {
                                $SettingSelect = new SettingSelect();
                                $SettingSelect->id_setting = $settings->id;
                                $SettingSelect->value = $el_new;
                                $SettingSelect->priority = $i;
                                $SettingSelect->save();
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        //if type double list
        if ($data['type'] == 3) {
            $i = 0;
            foreach ($data['select21'] as $k => $el) {
                $i++;
                if ($el) {
                    if (is_numeric($k)) {
                        $el = trim($el);
                        if ($el) {
                            $SettingSelect = SettingSelect::find($k);
                            $SettingSelect->id_setting = $settings->id;
                            $SettingSelect->value = $el;
                            $SettingSelect->value2 = $data['select22'][$k];
                            $SettingSelect->priority = $i;
                            $SettingSelect->save();
                        }
                    } else {
                        foreach ($data['select21']['new'] as $k_new => $el_new) {
                            $el_new = trim($el_new);
                            if ($el_new) {
                                $SettingSelect = new SettingSelect();
                                $SettingSelect->id_setting = $settings->id;
                                $SettingSelect->value = $el_new;
                                $SettingSelect->value2
                                    = $data['select22']['new'][$k_new];
                                $SettingSelect->priority = $i;
                                $SettingSelect->save();
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        //if the triple list
        if ($data['type'] == 5) {
            $i = 0;
            foreach ($data['select31'] as $k => $el) {
                $i++;
                if ($el) {
                    if (is_numeric($k)) {
                        $el = trim($el);
                        if ($el) {
                            $SettingSelect = SettingSelect::find($k);
                            $SettingSelect->id_setting = $settings->id;
                            $SettingSelect->value = $el;
                            $SettingSelect->value2 = $data['select32'][$k];
                            $SettingSelect->value3 = $data['select33'][$k];
                            $SettingSelect->priority = $i;
                            $SettingSelect->save();
                        }
                    } else {
                        foreach ($data['select31']['new'] as $k_new => $el_new) {
                            $el_new = trim($el_new);
                            if ($el_new) {
                                $SettingSelect = new SettingSelect();
                                $SettingSelect->id_setting = $settings->id;
                                $SettingSelect->value = $el_new;
                                $SettingSelect->value2
                                    = $data['select32']['new'][$k_new];
                                $SettingSelect->value3
                                    = $data['select33']['new'][$k_new];
                                $SettingSelect->priority = $i;
                                $SettingSelect->save();
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        self::reCacheSettings();

        return $settings;
    }

    public static function reCacheSettings()
    {
        Cache::tags('settings')->flush();
    }

    public static function doDelete($id)
    {
        if (is_numeric($id)) {
            $id_page = request('id');
            $page = self::find($id_page);

            Event::fire('setting.delete', [$page]);

            $page->delete();

            self::reCacheSettings();
        }
    }

    public static function isValid($data, $id)
    {
        self::$rules['slug'] .= $id;

        $validator = Validator::make($data, self::$rules);

        if ($validator->fails()) {
            return response()->json([
                'status'          => 'error',
                'errors_messages' => $validator->messages(),
            ]);
        }

        return false;
    }

    public function selectValues()
    {
        return $this->hasMany(SettingSelect::class, 'id_setting')
            ->orderBy('priority')
            ->get()
            ->toArray();
    }
}
