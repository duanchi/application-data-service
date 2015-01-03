<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 14/12/24
 * Time: 下午5:43
 */
namespace IO;

/**
 * Class HTTP
 * @package IO
 *
 * @todo change throw exception to class level error code.
 */

class HTTP
{
    private static $_instances      =   [];
    private static $_requests       =   [];

    CONST SEPARATOR                 =   ' ';
    CONST CR                        =   "\r";
    CONST LF                        =   "\n";
    CONST CRLF                      =   self::CR . self::LF;
    CONST CONNECT_TIMEOUT           =   0.5;
    CONST READ_TIMEOUT              =   0.6;

    public  static function add_request($_uri, $_options = []) {

        $_http_option           =   [
                                        'method'    =>  HTTP_GET,
                                        'version'   =>  'HTTP/1.1',
                                        'timeout'   =>  10,
                                        'request'   =>  NULL,
                                        'host'      =>  ''
                                    ];
        $__RESULT               =   make_uuid($_http_option['method']);

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
                                            'host'      => $_http_option['host'],
                                            'port'      => '80',
                                            'timeout'   => '10',
                                            'package'   => $_socket_package
                                        ];

        return $__RESULT;
    }

    public  static function handle() {

        $__RESULT                   =   FALSE;
        $_socket_instances          =   [];
        $_connect_timeout           =   self::CONNECT_TIMEOUT;
        $_read_timeout              =   self::READ_TIMEOUT;

        if (empty(self::$_requests)) ;
        else {
            foreach (self::$_requests as $_key => $_request) {

                $_socket_handle     = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
                $_connect_status    = $_socket_handle->connect($_request['host'], $_request['port'], $_connect_timeout, 0);

                if(!$_connect_status) {
                    echo "Connect Server fail.errCode=".$_socket_handle->errCode;
                } else {
                    $_socket_handle->send($_request['package']);
                    $_socket_instances[$_key] = $_socket_handle;
                }
            }

            while(!empty($_socket_instances)) {

                $_write             =   [];
                $_error             =   [];
                $_client_left       =   swoole_client_select($_socket_instances, $_write, $_error, $_read_timeout);

                if($_client_left > 0) {

                    foreach($_socket_instances as $_key => $_instance) {
                        $__RESULT[$_key]                = self::execute_receive($_instance);
                        unset($_socket_instances[$_key]);
                    }
                }
            }
        }

        return $__RESULT;
    }

    private static function execute_receive($_instance) {
        $__RESULT                   =   [
                                            'line'      => '',
                                            'header'    => [],
                                            'version'   => '',
                                            'status'    => '',
                                            'body'      => '',
                                            'body-length'   => 0
                                        ];
        //EXPLODE STREAM HEADER AND BODY
        $_tmp_stream                =   explode(self::CRLF.self::CRLF, $_instance->recv(), 2);

        if (count($_tmp_stream) == 2) {

            $_tmp_response_header   =   $_tmp_stream[0];
            $_tmp_response_body     =   $_tmp_stream[1];

        } else \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);

        $__RESULT                   =   array_merge(
                                            $__RESULT,
                                            self::execute_header($_tmp_response_header)
                                        );

        do {

            $_gzipped               =   (
                                            isset($__RESULT['header']['content-encoding'])
                                            and
                                            $__RESULT['header']['content-encoding'] == 'gzip'
                                        ) ?
                                            TRUE : FALSE;

            //HTTP 1.1 WITH TYPE CHUNKED
            if (
                $__RESULT['version'] == HTTP_VERSION_11
                and
                $__RESULT['header']['transfer-encoding'] == 'chunked'
            ) {
                $__RESULT           =   array_merge(
                                            $__RESULT,
                                            self::execute_body_type_chunked($_tmp_response_body, $_instance, $_gzipped)
                                        );
                break;
            }

            //HTTP 1.1 NORMAL WITH CONTENT-LENGTH
            if (
                $__RESULT['version'] == HTTP_VERSION_11
                and
                isset($__RESULT['header']['content-length'])
            ) {

                $__RESULT           =   array_merge(
                                            $__RESULT,
                                            self::execute_body_with_content_length($_instance->recv(), $__RESULT['header']['content-length'], $_instance, $_gzipped)
                                        );
                break;
            }
            //HTTP 1.1 NORMAL

        } while (FALSE);


        return $__RESULT;
    }

    private static function execute_header($_stream) {

        $__RESULT                   =   [
                                            'line'      => '',
                                            'header'    => [],
                                            'version'   => '',
                                            'status'    => '',
                                        ];
        $_tmp_header                =   explode(self::CRLF, $_stream);
        $__RESULT['line']           =   array_shift($_tmp_header);
        $_tmp_line                  =   explode(' ', $__RESULT['line'], 2);

        if (count($_tmp_line) == 2) {

            $__RESULT['version']    =   $_tmp_line[0];
            $__RESULT['status']     =   $_tmp_line[1];

        } else \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);

        foreach ($_tmp_header as $_value) {

            $_tmp_header            =   explode(': ', $_value, 2);

            count($_tmp_header) == 2 ?
                $__RESULT['header'][strtolower($_tmp_header[0])]   =   strtolower($_tmp_header[1])
                :
                $__RESULT['header'][strtolower($_tmp_header[0])]   =   strtolower($_tmp_header[0]);
        }

        return $__RESULT;
    }

    private static function execute_body_with_content_length($_stream, $_content_length, $_sock_instance = FALSE, $_gzipped = FALSE) {
        $__RESULT                   =   [
                                            'body'          => '',
                                            'body_length'   => 0
                                        ];
        $__RESULT['body']           =   $_stream;
        $_stream_length             =   strlen($_stream);
        $__RESULT['body-length']    =   ($_stream_length == $_content_length) ?
                                            $_content_length
                                            :
                                            strlen($_stream);

    }

    private static function execute_body_type_chunked($_stream, $_sock_instance = FALSE, $_gzipped = FALSE) {

        $__RESULT                   =   [
                                            'body'          => '',
                                            'body-length'   => 0
                                        ];
        $_response_eof              =   FALSE;
        $_tmp_stream                =   $_stream;

        do {
            parse_stream:

            $_tmp_current_stream    =   explode(self::CRLF, $_tmp_stream, 2);
            $_tmp_current_stream_len=   hexdec($_tmp_current_stream[0]);

            if ($_tmp_current_stream_len != 0) {

                $_tmp_stream        =   substr($_tmp_current_stream[1], $_tmp_current_stream_len + 2);
                $_tmp_current_stream=   substr($_tmp_current_stream[1], 0, $_tmp_current_stream_len);
                $__RESULT['body']  .=   $_tmp_current_stream;
                $__RESULT['body-length']   +=   $_tmp_current_stream_len;

                if (!empty($_tmp_stream))   goto parse_stream;

                $_tmp_stream        =   ($_sock_instance != FALSE) ? $_sock_instance->recv() : FALSE;
            } else {
                $_response_eof      =   TRUE;
                $_tmp_stream        =   FALSE;
            }

        } while (
                    !(
                        $_response_eof
                        or
                        ($_tmp_stream == FALSE)
                    )
                );

        return $__RESULT;
    }
}