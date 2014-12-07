<?php
/**
 * Created by PhpStorm.
 * User: Chi
 * Date: 2014/9/27
 * Time: 22:44
 */

namespace Core;


class ERR {
    static public function __callStatic($function_name, $arguments) {
        echo $function_name;
        var_dump($arguments);
    }
} 