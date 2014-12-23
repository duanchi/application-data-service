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

        //PACKAGE HTTP(S) REQUEST LINE
        $_socket_package    =   $_http_option['method'].
                                self::SPACE.
                                $_uri.
                                self::SPACE.
                                $_http_option['version'].
                                self::BR;

        //PACKAGE HTTP(S) REQUEST HEADER


        //PACKAGE HTTP(S) REQUEST BODY



        return self::process($_socket_package);
    }

    private static function process($_package) {
        $_result = NULL;
        $client = new swoole_client(SWOOLE_TCP | SWOOLE_KEEP);
        $client->on("connect", function($_cli) use ($_package) {
            $_cli->send($_package);
        });

        $client->on("receive", function($_cli, $_data) use ($_result) {
            $_result = $_data;
        });

        return $_result;
    }
}