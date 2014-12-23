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
	
	public static function fetch_raw_data($_request, $_conf) {
        $__RESULT       = FALSE;
        $_tmp_conf      = FALSE;

        //FETCH CONF WITH URI OR KEY
        foreach($_conf->roles as $_tmp_value) {
            if ($_request['key'] == NULL && $_tmp_value->request->uri == $_request['uri']) {
                $_tmp_conf  = $_tmp_value;
                break;
            } elseif ($_request['key'] != NULL && $_tmp_value->key == $_request['key']) {
                $_tmp_conf  = $_tmp_value;
                break;
            }
        }

        //FETCH URI WITH SCHEME
        switch($_tmp_conf->request->scheme) {
            case URI_SCHEME_TCP:

                break;

            case URI_SCHEME_HTTP:
            default:
                //PARSE PARAMETERS

                //SWOOLEING
                \IO\NETWORK::http(  $_tmp_conf->request->uri,
                                    [
                                        'method'    => HTTP_GET,
                                    ]
                );
                break;
        }



        return $__RESULT;
    }
}