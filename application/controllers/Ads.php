<?php
/**
 * File    application\controllers\Router.php
 * Desc    Api路由全流程处理模块
 * Manual  svn://svn.vop.com/api/manual/Controller/Router
 * version 1.1.2
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-23
 * Time    17:38
 */

/**
 * @name    ApiController
 * @author  duanChi <http://weibo.com/shijingye>
 * @desc    API路由控制器
 * @see     http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class AdsController extends Yaf\Controller_Abstract {
	
	public function indexAction($_URI = NULL) {

        if (\Yaf\Registry::get('__IS_AUTHORIZED')) {

            $__REQUEST          =   \Yaf\Registry::get('__REQUEST');
            $__APP              =   \Yaf\Registry::get('__APP');
            $__API              =   \Yaf\Registry::get('__API');
            $__RAW_DATA         =   \Yaf\Registry::get('__RAW_DATA');
            $__DATA             =   \Yaf\Registry::get('__DATA');
            $__RETURN_PACKEGE   =   NULL;

            //PLUGIN PROCESS START -->

            //PLUGIN PROCESS END <--

            //USE https://github.com/bupt1987/html-parser TO PARSE (X)HTML/XML

            //RESULT PACKAGE START -->
            //接口返回内容封装
            \Yaf\Registry::set('__RESPONSE', \Process\ApiModel::package($__DATA));
            //RESULT PACKAGE END <--

        }

		return FALSE;
	}
}