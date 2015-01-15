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
        $__RESULT      = FALSE;

        //FETCH CONF WITH URI OR KEY
        foreach($_conf->roles as $_tmp_value) {
            if ($_request['key'] == NULL && $_tmp_value->request->uri == $_request['uri']) {

            } elseif ($_request['key'] != NULL && $_tmp_value->key == $_request['key']) {

            } else goto no_match_role;

            $__RESULT  = $_tmp_value;

            no_match_role:

        }

        $__RESULT->request->host = self::parse_host($_request['uri'], $_conf);
        
        return $__RESULT;
    }
	
	public static function fetch_raw_data($_parameters) {
        $__RESULT       = FALSE;

        //FETCH URI WITH SCHEME
        switch($_parameters->request->scheme) {
            case URI_SCHEME_TCP:

                break;

            case URI_SCHEME_HTTP:
            default:
                //PARSE HOST


                //PARSE PARAMETERS
                if (!isset($_parameters->request->host)) \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);

                //SWOOLEING
                \IO\HTTP::add_request(  [
                                            'uri'       =>  $_parameters->request->uri,
                                            'method'    =>  HTTP_GET,
                                            'host'      =>  $_parameters->request->host
                                        ]);

                //\IO\HTTP::handle();
                //var_dump(\IO\HTTP::handle());

                break;
        }

        return $__RESULT;
    }

    private static function parse_host($_uri, $_conf) {
        $__RESULT           =   FALSE;

        return $__RESULT;
    }
}