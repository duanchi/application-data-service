<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/2/9
 * Time: 上午9:22
 */

namespace IO;


class HTTP2 {
    private static $_instances              =   [];
    private static $_requests               =   [];

    public  static function add_request($_options = []) {

        $_http_option                       =   [
                                                    'method'        =>  HTTP_GET,
                                                    'version'       =>  HTTP_VERSION_1_1,
                                                    'timeout'       =>  10,
                                                    'request-data'  =>  NULL,
                                                    'host'          =>  '',
                                                    'uri'           =>  '',
                                                    'headers'       =>  [],
                                                    'keepalive'     =>  TRUE
                                                ];
        $__RESULT                           =   make_uuid($_http_option['method']);
        $_request_handle                    =   NULL;


        foreach ($_http_option as $_key => $_value) isset($_options[$_key]) ? $_http_option[$_key] = $_options[$_key] : FALSE;

        /*
         * CHECK NESS OPTONS
         *
         * */

        switch($_http_option['method']) {
            case HTTP_GET   : $_http_option['method']   =   'GET'     ; break;
            case HTTP_POST  : $_http_option['method']   =   'POST'    ; break;
            case HTTP_DELETE: $_http_option['method']   =   'DELETE'  ; break;
            case HTTP_HEAD  : $_http_option['method']   =   'HEAD'    ; break;
            case HTTP_PUT   : $_http_option['method']   =   'PUT'     ; break;
            case HTTP_PATCH : $_http_option['method']   =   'PATCH'   ; break;
        }


        if(isset($_http_option['host']) and !empty($_http_option['host'])) {

            $_http_uri_handle                   =   new \http\Url($_http_option['uri']);
            $_http_option['headers']            =   array_merge($_http_option['headers'], ['Host'=>$_http_uri_handle->host]);
            $_http_uri_handle->host             =   $_http_option['host'];
            $_http_option['uri']                =   $_http_uri_handle->toString();

        }


        $_request_handle                    =   new \http\Client\Request(
                                                                            $_http_option['method'],
                                                                            $_http_option['uri'],
                                                                            $_http_option['headers'],
                                                                            $_http_option['request-data']
                                                                        );

        $_host                              =   parse_url($_http_option['uri'], PHP_URL_HOST)
;        $_request_handle->setOptions([
                                        'protocol'      =>  $_http_option['version'],
                                        'timeout'       =>  $_http_option['timeout'],
                                        'tcp_keepalive' =>  $_http_option['keepalive']
                                    ]);

        self::$_requests[$__RESULT]         =   $_request_handle;

        return $__RESULT;
    }

    public  static function handle() {

        $__RESULT                           =   FALSE;
        $_http_handle                       =   NULL;

        if (empty(self::$_requests)) ;
        else {

            $_http_handle                   =   new \http\Client();
            $_http_handle->enablePipelining(TRUE);
            $_http_handle->enableEvents(TRUE);

            foreach (self::$_requests as $_key => $_request) {
                $_http_handle->enqueue($_request);
                $__RESULT[$_key]            =   NULL;
            }

            while($_http_handle->once())
                $_http_handle->wait();

            while (
                    list($_key)             =   each($__RESULT)
                    and
                    $__RESULT[$_key]        =   self::parse_response($_http_handle->getResponse())
                  );
        }

        return $__RESULT;
    }

    private static function parse_response($_response) {

        if ($_response == FALSE) return FALSE;

        else {
            $__RESULT                           =   [
                                                        'line'              =>  '',
                                                        'header'            =>  $_response->getHeaders(),
                                                        'version'           =>  $_response->getHttpVersion(),
                                                        'status'            =>  $_response->getResponseCode(),
                                                        'status-message'    =>  $_response->getResponseStatus(),
                                                        'body'              =>  $_response->getBody()->serialize(),
                                                        'body-length'       =>  $_response->getHeader('Content-length')
                                                    ];

            return $__RESULT;
        }
    }
}