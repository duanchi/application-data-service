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
                $__RESULT  = $_tmp_value;
                break;
            } elseif ($_request['key'] != NULL && $_tmp_value->key == $_request['key']) {
                $__RESULT  = $_tmp_value;
                break;
            }
        }

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
                //PARSE PARAMETERS

                //SWOOLEING
                /*$__RESULT   = \IO\NETWORK::http(    $_parameters->request->uri,
                                                    [
                                                        'method'    => HTTP_GET,
                                                    ]
                );*/

                $_handle    = new \IO\HTTP($_parameters->request->uri);
                $_handle->onReady(function(){return;});
                $__RESULT   = $_handle->execute();
                break;
        }



        return $__RESULT;
    }
}