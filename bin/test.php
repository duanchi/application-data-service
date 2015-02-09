<?php
//var_dump(filter_var('/^http://localhost/login/login.action(.*)/', FILTER_VALIDATE_REGEXP));

setcookie('a','1');
setcookie('b','34');
setcookie('c','g');



Timespent::_init();
base64_encode(msgpack_pack([
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
]));
Timespent::record('IN-PROC');

//var_dump(Timespent::spent());

$_str = "%u806A%u660E%u7684%u4F60%u4E00%u5B9A%u77E5%u9053%u83B7%u53D6%u4E4B%u540E%u663E%u793A%u7A7A%u767D%u7684%u79D8%u5BC6";

//var_dump(unescape($_str));


header('spent:' . Timespent::spent());


var_dump(\http\Client\Curl\HTTP_VERSION_2_0);

//var_dump(microtime());

function unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i ++)
    {
        if ($str[$i] == '%' && $str[$i + 1] == 'u')
        {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
                if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) .
                        chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) .
                        chr(0x80 | (($val >> 6) & 0x3f)) .
                        chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
            if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
    }
    return $ret;
}






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
        $time = round((self::$stop_time - self::$start_time) * 1000, 3);
        !empty($_flag) ? self::$spend_time[$_flag] = $time : self::$spend_time[] = $time;
    }

    public static function spent() {
        self::total();
        $_result = '';
        foreach (self::$spend_time as $_key => $_node) {
            $_result .= $_key . ': ' . $_node . 'ms, ';
        }
        return $_result;
    }

    public static function total() {
        self::$spend_time['TOTAL'] = round((self::get_microtime() - self::$total_time) * 1000, 3);
    }

}