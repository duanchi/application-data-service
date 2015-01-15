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
	
	public static function parse($_app) {
        $__RESULT       = FALSE;
        $_tmp_config    = self::get_config($_app);
        $__RESULT       = $_tmp_config;

        return $__RESULT;
    }

    private static function get_config($_app) {
        $__RESULT               =   FALSE;

        $_conf_directory        =   ADS_APPS_CONFIG . DIRECTORY_SEPARATOR . $_app->appkey . DIRECTORY_SEPARATOR;
        $_licence_config        =   get_yaf_config($_conf_directory . 'licence.ini');


        if (    isset($_licence_config->licence->key)
                and
                $_licence_config->licence->key == $_app->licence
            ) {

            $_h_conf            =   get_yaf_config($_conf_directory . 'conf.h.ini');

            if (isset($_h_conf->etc->constent))
                foreach ($_h_conf->etc->constent as $_key => $_constant) define($_key, $_constant);

            $_conf              =   get_yaf_config($_conf_directory . 'conf.ini');


            $_conf              =   $_conf->toArray();
            $_conf['etc']['hosts']  =   $_h_conf->etc->hosts->toArray();

            $__RESULT           =   $_conf;
        }

        //var_dump($__RESULT);
        return $__RESULT;
    }
}