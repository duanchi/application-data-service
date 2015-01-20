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
class ConfigModel {
	
	public static function parse($_app, $_request) {
        $__RESULT           = FALSE;
        $_tmp_config        = self::get_config($_app, $_request);
        $__RESULT           = $_tmp_config;

        return $__RESULT;
    }

    private static function get_config($_app, $_request) {
        $__RESULT                   =   FALSE;
        $_conf_directory            =   ADS_APPS_CONFIG . DIRECTORY_SEPARATOR . $_app->appkey . DIRECTORY_SEPARATOR;
        $_licence_config            =   get_yaf_config($_conf_directory . 'licence.ini');


        if (    isset($_licence_config->licence->key)
                and
                $_licence_config->licence->key == $_app->licence
            ) {

            $_h_conf                =   get_yaf_config($_conf_directory . 'conf.h.ini');

            if (isset($_h_conf->etc->constant))
                foreach ($_h_conf->etc->constant as $_key => $_constant) define($_key, $_constant);

            $_conf                  =   get_yaf_config($_conf_directory . 'conf.ini')->toArray();
            $_role                  =   self::get_request_config($_conf['roles'], $_request);

            unset($_conf['roles']);

            if ($_role !== FALSE)
                $_conf['role']      =   $_role;

            $_conf['etc']['hosts']  =   $_h_conf->etc->hosts->toArray();

            $__RESULT               =   $_conf;
        }

        return $__RESULT;
    }

    private static function get_request_config($_roles, $_request) {

        $__RESULT                   =   FALSE;
        //FETCH CONF WITH URI OR KEY
        foreach($_roles as $_tmp_value) {

            if (
                $_request['key'] == NULL
                and
                isset($_tmp_value['request']['type'])
                and
                $_tmp_value['request']['type'] == 'regex'
                and
                preg_match_all($_tmp_value['request']['uri'], $_request['uri'], $__matches)
            ) {
                $_tmp_value['request']['map']   =   $__matches;
                $_tmp_value['request']['uri']   =   $_request['uri'];
            } elseif (
                $_request['key'] == NULL
                and
                $_tmp_value['request']['uri'] == $_request['uri']
            ) {

            } elseif (
                $_request['key'] != NULL
                and
                $_tmp_value['key'] == $_request['key']
            ) {

            } else goto no_match_role;

            $__RESULT               =   $_tmp_value;
            break;

            no_match_role:

        }

        return $__RESULT;
    }
}