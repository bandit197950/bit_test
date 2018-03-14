<?php

namespace bit_test\www\conf;

include_once "conf/config.php";

final class Utils
{
    public static function Startup()
    {
        //magic quotes fix
        if (get_magic_quotes_gpc()) {
            function fix_magicQuotes(&$value, $key)
            {
                $value = stripslashes($value);
            }

            $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
            array_walk_recursive($gpc, 'fix_magicQuotes');
        }

        $doc_root = $_SERVER['DOCUMENT_ROOT'] . Config::doc_path();
        $dir_sep = ($_SERVER['COMSPEC']) ? ';' : ':';

        $path = '';

        if (!empty(Config::include_dir())) {
            foreach (Config::include_dir() as $include_dir) {
                $path .= $doc_root . $include_dir . $dir_sep;
            }
        } else {
            $path = $doc_root;
        }

        ini_set('include_path', $path);

        spl_autoload_register(function ($class_name) {
            $filename = strtolower($class_name) . ".php";
            $filename = ltrim($filename, "bit_test\\www\\");
            if (!file_exists($filename)) {
                throw new \Exception("Can't find class: " . $class_name);
            }
            include_once $filename;
        });
    }

    public static function ShowException(\Exception $e)
    { ?>
        <style type="text/css">
            td.tstack {
                border-top: solid 1px #777;
            }
            td.tstack-trace {
                border-top: solid 1px #777;border-left: solid 1px #777;
            }
        </style>
        <table style="border:2px solid #777" cellpadding="5px;" cellspacing="0px;">
            <tr>
                <th colspan=2 style="background-color:red; color:white; font-weight:bold; font-size:22px;">Exception
                    Info
                </th>
            </tr>
            <tr>
                <td>Code:</td>
                <td><?php echo $e->getCode(); ?></td>
            </tr>
            <tr>
                <td>Message:</td>
                <td><?php echo $e->getMessage(); ?></td>
            </tr>
            <tr>
                <td>File:</td>
                <td><?php echo $e->getFile(); ?></td>
            </tr>
            <tr>
                <td>Line:</td>
                <td><?php echo $e->getLine(); ?></td>
            </tr>
            <tr>
                <td class="tstack">Trace stack:</td>
                <td class="tstack-trace">
                    <pre><?php var_dump($e->getTrace()); ?></pre>
                </td>
            </tr>
        </table>

    <?php }
}