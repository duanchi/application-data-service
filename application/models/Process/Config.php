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
        $__RESULT   = FALSE;

        $_tmp_config    = get_yaf_config(ADS_APPS_CONFIG . DIRECTORY_SEPARATOR . $_app->appkey . '.ini');
        (isset($_tmp_config->application->licence) && $_tmp_config->application->licence == $_app->licence) ? $__RESULT = $_tmp_config : FALSE;

        return $__RESULT;
    }
}