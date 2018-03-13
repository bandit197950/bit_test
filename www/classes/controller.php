<?php

namespace bit_test\www\classes;

use bit_test\www\conf\config;

abstract class Controller extends Strict
{
    protected $Error = array();

    public abstract function Index();

    private static function Load($path, $name, $prefix)
    {
        $full_class_name = '\\bit_test\\www\\' . $path . '\\' . $prefix . $name;
        return new $full_class_name;
    }

    public static function LoadModel($model_name)
    {
        return self::Load('model', $model_name, '');
    }

    public static function LoadView($view_name)
    {
        return self::Load('view', $view_name, '');
    }

    public static function GetLocation($location)
    {
        return Config::doc_path() . $location;
    }

    public static function ChangeLocation($location)
    {
        $location = self::GetLocation($location);
        header("Location: ${location}");
        exit();
    }

}