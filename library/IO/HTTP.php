<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 14/12/24
 * Time: 下午5:43
 */
namespace IO;

class HTTP
{
    private static $_instances      =   [];
    private static $_requests       =   [];

    CONST SEPARATOR                 =   ' ';
    CONST CR                        =   "\r";
    CONST LF                        =   "\n";
    CONST CRLF                      =   CR . LF;
    CONST CONNECT_TIMEOUT           =   0.5;

    public  static function add_request($_uri, $_options = []) {

        $_http_option           =   [
                                        'method'    => HTTP_GET,
                                        'version'   => 'HTTP/1.1',
                                        'timeout'   => 10,
                                        'request'   => NULL,
                                    ];
        $__RESULT               =   make_uuid($_http_option['method']);

        foreach ($_http_option as $_key => $_value) isset($_options[$_key]) ? $_http_option[$_key] = $_options[$_key] : FALSE;

        switch($_http_option['method']) {
            case HTTP_GET   : $_http_option['method']   = 'GET'     ; break;
            case HTTP_POST  : $_http_option['method']   = 'POST'    ; break;
            case HTTP_DELETE: $_http_option['method']   = 'DELETE'  ; break;
            case HTTP_HEAD  : $_http_option['method']   = 'HEAD'    ; break;
            case HTTP_PUT   : $_http_option['method']   = 'PUT'     ; break;
            case HTTP_PATCH : $_http_option['method']   = 'PATCH'   ; break;
        }

        $_uri   = '/env.php';
        $_host  = 'http://api.ads.devel';

        //PACKAGE HTTP(S) REQUEST LINE
        /*$_socket_package        =   $_http_option['method'].
            self::SPACE.
            $_uri.
            self::SPACE.
            $_http_option['version'].
            self::BR;*/

        //PACKAGE HTTP(S) REQUEST HEADER
        //$_socket_package       .=  'Host: ' . $_host;

        //PACKAGE HTTP(S) REQUEST BODY

        $_socket_package    = 'GET /env.php HTTP/1.1
Host: api.ads.devel
Connection: keep-alive
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36
Referer: http://baidu.com/
Accept-Encoding: gzip, deflate, sdch
Accept-Language: zh-CN,zh;q=0.8,en;q=0.6,ja;q=0.4
Cookie: BAIDUID=861720F2CFE8CCE349580E417B3BF241:FG=1

';


        self::$_requests[$__RESULT] =   [
                                        'host'      => '127.0.0.1',
                                        'port'      => '80',
                                        'timeout'   => '10',
                                        'package'   => $_socket_package
                                    ];

        return $__RESULT;
    }

    public  static function handle() {

        $__RESULT       = FALSE;
        $_socket_instances   = [];

        if (empty(self::$_requests)) ;
        else {
            foreach (self::$_requests as $_key => $_request) {
                $_socket_handle = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
                $_connect_status = $_socket_handle->connect('127.0.0.1', 9501, 0.5, 0);
                if(!$_connect_status)
                {
                    echo "Connect Server fail.errCode=".$_socket_handle->errCode;
                }
                else
                {
                    $_socket_handle->send("HELLO WORLD\n");
                    $_socket_instances[$_key] = $_socket_handle;
                }
            }

            while(!empty($_socket_instances))
            {
                $write = $error = array();
                $read = array_values($_socket_instances);
                $n = swoole_client_select($read, $write, $error, 0.6);
                if($n > 0)
                {
                    foreach($read as $index=>$c)
                    {
                        echo "Recv #{$c->sock}: ".$c->recv()."\n";
                        unset($_socket_instances[$index]);
                    }
                }
            }
        }

        return $__RESULT;
    }
}