<?php

namespace bit_test\www\conf;

/**
 * @method static Config db_host()
 * @method static Config db_name()
 * @method static Config db_login()
 * @method static Config db_pwd()
 * @method static Config include_dir()
 * @method static Config doc_path()
 * @method static Config class_path()
 */
final class Config
{
    private static $conf = [
        'db_host' => 'localhost',
        'db_name' => 'bit_test',
        'db_login' => 'super_admin',
        'db_pwd' => '#superadmin2018#',
        'include_dir' => ['/', '/lib/', '/classes/', '/conf/', '/controller/', '/model/'],
        'doc_path' => '/bit_test/www/',
        'class_path' => '\\bit_test\\www\\'
    ];

    public static function __callStatic($name, $arguments)
    {
        $ret = null;
        if (isset(self::$conf[$name])) {
            $ret = self::$conf[$name];
        }
        return $ret;
    }
}