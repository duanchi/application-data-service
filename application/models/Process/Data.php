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

        var_dump($_request, $_conf);
        foreach($_conf as $_tmp_value) {
            if ($_tmp_value->key == NULL && $_tmp_value->uri == $_request['uri']) {

            } else {

            }
        }
        //FETCH CONF WITH URI OR KEY


        //FETCH URI WITH SCHEME

        return $__RESULT;
    }
}