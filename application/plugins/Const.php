<?php
/**
 * File    application\plugin\Ads.php
 * Desc    请求预处理插件模块
 * Manual  svn://svn.vop.com/api/manual/plugin/Process
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-22
 * Time    20:36
 */

class ConstPlugin extends Yaf\Plugin_Abstract {

	function __construct() {
		//parse app path
		$_const = \CONF::get('const');
		while (list($__key, $__value) = each($_const)) define('ADS_' . strtoupper($__key), $__value);
	}
}