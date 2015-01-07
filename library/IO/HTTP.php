<?php
/**
 * Created by PhpStorm.
 * User: duanchi
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

    public  static function add_request($_options = []) {

        $_http_option           =   [
                                        'method'    =>  HTTP_GET,
                                        'version'   =>  'HTTP/1.1',
                                        'timeout'   =>  10,
                                        'request'   =>  NULL,
                                        'host'      =>  '',
                                        'uri'       =>  '',
                                        'headers'   =>  []
                                    ];
        $__RESULT               =   make_uuid($_http_option['method']);
        $_CRLF                  =   self::CRLF;
        $_SEPARATOR             =   self::SEPARATOR;

        foreach ($_http_option as $_key => $_value) isset($_options[$_key]) ? $_http_option[$_key] = $_options[$_key] : FALSE;

        //CHECK OPTOINS
        $_REQUEST_URI           =   parse_url($_http_option['uri']);

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

        $_request_path              =   (   isset($_REQUEST_URI['path'])
                                            and
                                            !empty($_REQUEST_URI['path'])
                                        ) ?
                                            $_REQUEST_URI['path'] : '/';

        //PACKAGE HTTP(S) REQUEST LINE
        $_socket_package            =   $_http_option['method'] .
                                        $_SEPARATOR .
                                        $_request_path .
                                        $_SEPARATOR .
                                        $_http_option['version'] .
                                        $_CRLF;

        //PACKAGE HTTP(S) REQUEST HEADER
        $_socket_package           .=   'Host: '.$_REQUEST_URI['host'] . $_CRLF;

        if (!empty($_http_option['headers'])) {
            foreach($_http_option['headers'] as $_header)
                $_socket_package   .=   $_header . $_CRLF;
        }

        $_socket_package           .=    $_CRLF;



        //PACKAGE HTTP(S) REQUEST BODY

        //$_socket_package    = 'GET /env.php HTTP/1.1
//Host: api.ads.devel
//Connection: keep-alive
//Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
//User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36
//Referer: http://baidu.com/
//Accept-Encoding: gzip, deflate, sdch
//Accept-Language: zh-CN,zh;q=0.8,en;q=0.6,ja;q=0.4
//Cookie: BAIDUID=861720F2CFE8CCE349580E417B3BF241:FG=1

//';


        self::$_requests[$__RESULT] =   [
                                            'host'      => $_http_option['host'],
                                            'port'      => (    isset($_REQUEST_URI['port'])
                                                                    and
                                                                !empty($_REQUEST_URI['port'])
                                                            ) ?
                                                            $_REQUEST_URI['port'] : 80,
                                            'timeout'   => '10',
                                            'package'   => $_socket_package
                                        ];
        var_dump(self::$_requests);
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
                    file_put_contents(APPLICATION_PATH. '/cache/cache.log', "Connect Server fail.errCode=".$_socket_handle->errCode."\r\n\r\n\r\n", FILE_APPEND);
                    exit();
                } else {
                    $_socket_handle->send($_request['package']);
                    file_put_contents(APPLICATION_PATH. '/cache/cache.log', 'aaa44'."\r\n\r\n\r\n", FILE_APPEND);
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
        $_gzipped                   =   FALSE;
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
                isset($__RESULT['header']['transfer-encoding'])
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
                                            self::execute_body_with_content_length($_tmp_response_body, $__RESULT['header']['content-length'], $_instance, $_gzipped)
                                        );
                break;
            }
            //HTTP 1.1\1.0 NORMAL
            if (
                $__RESULT['version'] == HTTP_VERSION_11
                or
                $__RESULT['version'] == HTTP_VERSION_10
            ) {
                $__RESULT           =   array_merge(
                                            $__RESULT,
                                            self::execute_body_normal($_tmp_response_body, $_gzipped)
                                        );
                break;
            }

        } while (FALSE);

        if ($_gzipped) {
            $__RESULT['body']       =   self::decompress($__RESULT['body']);
            $__RESULT['body-length']=   strlen($__RESULT['body']);
        }

        return $__RESULT;
    }

    private static function execute_header($_stream) {

        $__RESULT                   =   [
                                            'line'      => '',
                                            'header'    => [],
                                            'version'   => '',
                                            'status'    => '',
                                            'status-code'   =>  '',
                                        ];
        $_tmp_header                =   explode(self::CRLF, $_stream);
        $__RESULT['line']           =   array_shift($_tmp_header);
        $_tmp_line                  =   explode(' ', $__RESULT['line'], 3);

        if (count($_tmp_line) == 3) {

            $__RESULT['version']    =   $_tmp_line[0];
            $__RESULT['status-code']=   $_tmp_line[1];
            $__RESULT['status']     =   $_tmp_line[2];

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

    private static function execute_body_normal($_stream, $_gzipped = FALSE) {
        $__RESULT                   =   [
                                            'body'          => '',
                                            'body_length'   => 0
                                        ];
        $__RESULT['body']           =   $_stream;
        $__RESULT['body-length']    =   strlen($_stream);

        return $__RESULT;
    }

    private static function execute_body_with_content_length($_stream, $_content_length, $_sock_instance = FALSE, $_gzipped = FALSE) {

        $__RESULT                   =   [
                                            'body'          => '',
                                            'body-length'   => 0
                                        ];
        $__RESULT['body']           =   $_stream;
        $__RESULT['body-length']   +=   strlen($_stream);


        //@todo timeout;
        /*parse_extra_stream:
        if ($__RESULT['body-length'] < $_content_length) {
            $_tmp_stream            =   $_sock_instance->recv();
            $__RESULT['body']      .=   $_tmp_stream;
            $__RESULT['body-length']   +=   strlen($_tmp_stream);
            //goto parse_extra_stream;
        }*/

        return $__RESULT;
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

    private static function decompress($_stream, $_compress_type = COMPRESS_TYPE_GZIP) {
        $__RESULT                   =   FALSE;

        switch ($_compress_type) {
            case COMPRESS_TYPE_ZIP:

                break;

            case COMPRESS_TYPE_GZIP:
            default:
                $__RESULT           =   gzdecode($_stream);
                break;
        }

        return $__RESULT;
    }
}