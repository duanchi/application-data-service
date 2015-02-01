<?php
//var_dump(filter_var('/^http://localhost/login/login.action(.*)/', FILTER_VALIDATE_REGEXP));

setcookie('a','1');
setcookie('b','34');
setcookie('c','g');


Timespent::_init();
var_dump(base64_encode(msgpack_pack([
    [
        'BAIDUID'=>'802F68D4DDFE20D1DBB27D8EB3CBB98E:FG=1',
        'expires'=>'Thu, 31-Dec-37 23:55:55 GMT',
        'max-age'=>'2147483647',
        'path'=>'/',
        'domain'=>'.baidu.com'
    ],
    [
        'BAIDUPSID'=>'802F68D4DDFE20D1DBB27D8EB3CBB98E',
        'expires'=>'Thu, 31-Dec-37 23:55:55 GMT',
        'max-age'=>'2147483647',
        'path'=>'/',
        'domain'=>'.baidu.com'
    ]
])));
Timespent::record('IN-PROC');

var_dump(Timespent::spent());


var_dump(get_defined_functions());













class Timespent {
    private static $start_time = 0;
    private static $stop_time = 0;
    private static $spend_time = [];
    private static $total_time = 0;

    public static function _init() {
        self::$total_time = self::get_microtime();
        self::start();
    }

    public static function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }

    public static function start()
    {
        self::$start_time = self::get_microtime();
    }

    public static function suspend($_flag = 'FINISH')
    {
        self::$stop_time = self::get_microtime();
        self::_spent($_flag);
    }

    public static function record($_flag = 'FINISH')
    {
        self::suspend($_flag);
        self::start();
    }

    private static function _spent($_flag = '')
    {
        $time = round((self::$stop_time - self::$start_time) * 1000, 1);
        !empty($_flag) ? self::$spend_time[$_flag] = $time : self::$spend_time[] = $time;
    }

    public static function spent() {
        self::total();
        return self::$spend_time;
    }

    public static function total() {
        self::$spend_time['TOTAL'] = round((self::get_microtime() - self::$total_time) * 1000, 1);
    }

}