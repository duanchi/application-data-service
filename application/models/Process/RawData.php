<?php
/**
 * File    libraries\VOP\Authorize.php
 * Desc    认证类实现文件
 * Manual  svn://svn.vop.com/api/manual/VOP/Authorize
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-10-29
 * Time    15:36
 */

namespace Process;
/**
 * Class Authorize
 * @package Process
 */
class RawDataModel {

    public static function parse_parameters($_request, $_conf) {
        $__RESULT                   =   [
                                            'uri'   =>  [],
                                            'host'  =>  '',
                                            'port'  =>  80,
                                            'method'=>  HTTP_GET,
                                            'timeout'   =>  10,
                                            'connect-timeout'   =>  0.5,
                                            'request'      =>  NULL,
                                            'cookie'            =>  NULL,
                                            'authorize'         =>  ['user'=>NULL,'password'=>NULL],
                                            'header'            =>  []
                                        ];

        $__RESULT['uri']            =   $_conf['role']['request']['uri'];
        $__RESULT['host']           =   self::parse_host($_request['uri'], $_conf['etc']['hosts']);
        $__RESULT['port']           =   isset($__RESULT['request']['uri']['port']) ? $__RESULT['request']['uri']['port'] : 80;
        $__RESULT['method']           =   $_request['method'];
        $__RESULT['request']        =   $_request['request'];
        $__RESULT['cookie']         =   $_request['cookie'];
        $__RESULT['header']         =   $_request['header'];

        return $__RESULT;
    }
	
	public static function fetch_raw_data($_parameters) {
        $__RESULT                   =   FALSE;
        $__REQUEST_ID               =   FALSE;

        //FETCH URI WITH SCHEME
        switch($_parameters['uri']['scheme']) {
            case URI_SCHEME_TCP:

                break;

            case URI_SCHEME_HTTP:
            default:
                //PARSE HOST


                //PARSE PARAMETERS
                if (!isset($_parameters['host'])) \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);

                //SWOOLEING

                $__REQUEST_ID=  \IO\HTTP::add_request(  [
                                            'uri'       =>  $_parameters['uri']['raw'],
                                            'method'    =>  $_parameters['method'],
                                            'host'      =>  $_parameters['host']
                                        ]);

                \Devel\Timespent::record('PRE-PROC');
                if ($__REQUEST_ID != FALSE) $__RESULT['data']   =   \IO\HTTP::handle()[$__REQUEST_ID];


                break;
        }

        return $__RESULT;
    }

    private static function parse_host($_uri, $_conf) {

        $__RESULT                   =   FALSE;
        $_resource                  =   parse_url($_uri);

        if (isset($_resource['host'])) {
            $_resource['host']      =   strtolower($_resource['host']);

            if (filter_var($_resource['host'], FILTER_VALIDATE_IP)) {
                $__RESULT           =   $_resource['host'];
            } else {

                $__RESULT           =   self::match_host_node($_resource['host'], $_conf);





                if (
                        $_resource['host'] == 'localhost'
                        or
                        $_resource['host'] == 'localhostadmin'
                ) {
                    $__RESULT       =   '127.0.0.1';
                }
            }
        }

        return $__RESULT;
    }

    private static function match_host_node ($_host, $_conf) {

        $_RESULT                    =   FALSE;
        $_host_stack                =   explode('.', $_host);
        $_host_match                =   FALSE;
        $_current_conf              =   $_conf;

        while ($_host_node  =   array_pop($_host_stack)) {

            do {

                if (isset($_current_conf[$_host_node])) {

                    $_current_conf  =   $_current_conf[$_host_node];
                    $_host_match    =   TRUE;

                    break;
                }

                if (isset($_current_conf['*'])) {

                    $_current_conf  =   $_current_conf['*'];
                    $_host_match    =   TRUE;

                    break;
                }

                $_host_match        =   FALSE;
                break 2;

            } while (TRUE);

        }

        if ($_host_match == TRUE) {
            if (!is_array($_current_conf)) {

                $_RESULT            =   $_current_conf;

            } elseif (isset($_current_conf['@'])) {

                $_RESULT            =   $_current_conf['@'];

            } elseif (isset($_current_conf['*'])) {

                $_RESULT            =   $_current_conf['*'];

            } else $_RESULT         =   FALSE;

        }

        return $_RESULT;
    }
}