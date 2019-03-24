<?php

namespace Vis\Builder\Services;

use Illuminate\Support\Str;
use Vis\Builder\System\AbstractDefinition;
use Vis\Builder\System\ConfigDefinitionAdapter;

class DefinitionLoader
{
    public static function get(string $page) : ?AbstractDefinition
    {
        if (static::exist($page)) {
            return new (static::getPath($page));
        }

        return null;
    }

    public static function exist(string $page) : bool
    {
        return class_exists(static::getPath($page));
    }

    public static function load(string $page) : ?AbstractDefinition
    {
        if (static::exist($page)) {
            return static::get($page);
        }

        $table = preg_replace('~\.~', '/', $page);
        $path = config_path("/builder/tb-definitions/$table.php");

        if (!file_exists($path)) {
            throw new \RuntimeException("Definition [{$path}] does not exist.");
        }

        return new ConfigDefinitionAdapter(require_once $path);
    }

    protected static function getPath($page)
    {
        $namespace = rtrim(config('definition_namespace', '\\App\\Definitions'), '\\');

        $definitionClass = Str::snake($page) . 'Definition';

        return $namespace . '\\' . $definitionClass;
    }
}
