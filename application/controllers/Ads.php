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

        if (\Core\KEY::get('_IS_AUTHORIZED')) {

            $__REQUEST           = \Core\KEY::get('_REQUEST');
            $__APP               = \Core\KEY::get('_APP');
            $__API               = \Core\KEY::get('_API');;
            $__RESULT            = FALSE;
            $__RETURN_PACKEGE    = NULL;


            //API PROCESS START -->
            $_RESULT = \Process\ApiModel::process($_API, $_REQUEST);
            //API PROCESS END <--

            //RESULT PACKAGE START -->
            //接口返回内容封装
            \Core\KEY::set('_RESPONSE', \Process\ApiModel::package($_RESULT));
            //RESULT PACKAGE END <--

        }

		return FALSE;
	}
}
