<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 14/12/23
 * Time: 下午3:27
 */

namespace IO;


class NETWORK {

    CONST SPACE     = ' ';
    CONST BR        = "\r\n";
    private static $i      = 0;
    private static $___result  = '';

    public static function http($_uri, $_option = NULL) {
        $_http_option = [
            'method'    => HTTP_GET,
            'version'   => 'HTTP/1.1',
            'timeout'   => 10,
            'request'   => NULL,
        ];

        foreach ($_http_option as $_key => $_value) isset($_option[$_key]) ? $_http_option[$_key] = $_option[$_key] : FALSE;

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
        $_socket_package    =   $_http_option['method'].
                                self::SPACE.
                                $_uri.
                                self::SPACE.
                                $_http_option['version'].
                                self::BR;

        //PACKAGE HTTP(S) REQUEST HEADER
        $_socket_package    .=  'Host: ' . $_host;

        //PACKAGE HTTP(S) REQUEST BODY



        return self::process($_socket_package);
    }

    private static function process($_package) {
        $_result = NULL;
        $_package = 'GET /env.php HTTP/1.1
Host: api.ads.devel
Connection: keep-alive
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36
Referer: http://baidu.com/
Accept-Encoding: gzip, deflate, sdch
Accept-Language: zh-CN,zh;q=0.8,en;q=0.6,ja;q=0.4
Cookie: BAIDUID=861720F2CFE8CCE349580E417B3BF241:FG=1

';


        $client = new \swoole_client(SWOOLE_TCP/*, SWOOLE_SOCK_ASYNC*/);

        /*$client->on("connect", function($_cli) use ($_package) {
            $_cli->send($_package);
            echo '1';
        });

        $_result = '';

        $i = 0;

        $client->on("send", function($_cli) {
            $_cli->recv();
            echo '3';
            $_cli->recv();
            echo '4';
            $_cli->close();
        });
        $client->on("receive", function($_cli, $_data) use ($_result) {
            self::$___result .= $_data;
            echo '2';
        });

        $client->on("error", function($cli) {
            //var_dump(socket_strerror($cli->errCode));
        });

        $client->on("close", function($cli) {
            for ($i=0; $i < 3; $i++) $cli->recv();
            echo 'close';
        });


        $client->connect('127.0.0.1', 80, 0.5);
        //$_result = $client->recv();

        //$client->close();

        var_dump(self::$___result);*/

        $status = 0;



        if ($client->connect('127.0.0.1', 80, 0.5)) {
            $status = $client->send($_package);
            var_dump($_package);
        } else {
            echo "connect failed.";
        }

        $_result = '';

        //$_result = $client->recv();
        /*for ($i = 0; $i < 4; $i++) {
            try {
                $tmp_stream = $client->recv();
            } catch (\Exception $e) {

            }

            var_dump(strlen($tmp_stream));
            $_result .= $tmp_stream;
        }*/

        $_tmp_stream    = $client->recv();

        $_response                      = [];

        list(   $_tmp_response_header,
                $_response['body']
            )                           = explode("\r\n\r\n", $_tmp_stream);

        $_tmp_response_header           = explode("\r\n", $_tmp_response_header);
        $_response['line']     = array_shift($_tmp_response_header);
        $_response['header']   = $_tmp_response_header;

        $client->close();

        file_put_contents(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'cache.log', $_result, FILE_APPEND);
        return $_response;
    }
}