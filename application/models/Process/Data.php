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
class DataModel {

    public static function parse_parameters($_request, $_conf) {
        $__RESULT                   =   FALSE;
        $__matches                  =   [];

        $__RESULT                   =   $_conf['role'];
        $__RESULT['request']['host']= self::parse_host($_request['uri'], $_conf['etc']['hosts']);

        return $__RESULT;
    }
	
	public static function fetch_raw_data($_parameters) {
        $__RESULT           =   FALSE;

        //FETCH URI WITH SCHEME
        switch($_parameters['request']['scheme']) {
            case URI_SCHEME_TCP:

                break;

            case URI_SCHEME_HTTP:
            default:
                //PARSE HOST


                //PARSE PARAMETERS
                if (!isset($_parameters['request']['host'])) \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);

                //SWOOLEING
                \IO\HTTP::add_request(  [
                                            'uri'       =>  $_parameters['request']['uri'],
                                            'method'    =>  HTTP_GET,
                                            'host'      =>  $_parameters['request']['host']
                                        ]);

                \Devel\Timespent::record('PRE-PROC');
                $__RESULT   =   \IO\HTTP::handle();

                break;
        }

        return $__RESULT;
    }

    private static function parse_host($_uri, $_conf) {

        $__RESULT           =   FALSE;
        $_resource          =   parse_url($_uri);

        if (isset($_resource['host'])) {
            $_resource['host']  =   strtolower($_resource['host']);

            if (filter_var($_resource['host'], FILTER_VALIDATE_IP)) {
                $__RESULT   =   $_resource['host'];
            } else {

                $__RESULT   =   self::match_host_node($_resource['host'], $_conf);





                if (
                        $_resource['host'] == 'localhost'
                        or
                        $_resource['host'] == 'localhostadmin'
                ) {
                    $__RESULT   =   '127.0.0.1';
                }
            }
        }

        return $__RESULT;
    }

    private static function match_host_node ($_host, $_conf) {

        $_RESULT            =   FALSE;
        $_host_stack        =   explode('.', $_host);
        $_host_match        =   FALSE;
        $_current_conf      =   $_conf;

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

            } else {

                isset($_current_conf['@'])
                ?
                $_RESULT            =   $_current_conf['@'] : FALSE;

            }

        }

        return $_RESULT;
    }
}