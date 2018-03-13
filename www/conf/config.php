<?php

namespace bit_test\www\conf;

final class Config
{
    private static $conf = [
        'db_host' => 'localhost',
        'db_name' => 'bit_test',
        'db_login' => 'super_admin',
        'db_pwd' => '#superadmin2018#',
        'include_dir' => ['/', '/lib/', '/classes/', '/conf/', '/controller/', '/model/'],
        'doc_path' => '/bit_test/www/'
    ];

    public static function __callStatic($name, $arguments)
    {
        $ret = null;
        if(isset(self::$conf[$name])) {
            $ret = self::$conf[$name];
        }
        return $ret;
    }
}