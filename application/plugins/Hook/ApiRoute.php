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
namespace Hook;

class ApiRoutePlugin extends \Yaf\Plugin_Abstract {

	public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {

		//PRETREATMENT REQUEST START -->
		if ($request->controller == 'Api' && \Core\KEY::get('_IS_AUTHORIZED')) {

            $_REQUEST = \Core\KEY::get('_REQUEST');

            //API ROUTER START -->
            //路由到对应api from api_config_devel.ini

            $_API = \Process\ApiModel::get($_REQUEST['api']['service'], $_REQUEST['api']['method'], $_REQUEST['method']);

            if ($_API == FALSE || empty($_API)) throw new \Exception('API_ROUTE_ERROR');
            //API ROUTER END <--

            \Core\KEY::set('_API', $_API);

        }
		//PRETREATMENT REQUEST END <--

	}
} 