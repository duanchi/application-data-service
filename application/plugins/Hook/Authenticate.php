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

class AuthenticatePlugin extends \Yaf\Plugin_Abstract {

	public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {

		if ($request->controller == 'Api') {

            $_REQUEST = \Core\KEY::get('_REQUEST');

            \Core\KEY::set('_IS_AUTHORIZED', FALSE);

			//AUTHENTICATE START -->
			//应用认证,appkey,appsecret,ip,count from authorize_config.ini
			$_APP = \Process\AuthorizeModel::authenticate($_REQUEST['access-token'], $_REQUEST['client-ip']);

			if (empty($_APP) || $_APP == FALSE) {
                throw new \Exception('AUTHENTICATE_FAILURE');
            }
            else {
                \Core\KEY::set('_IS_AUTHORIZED', TRUE);
                \Core\KEY::set('_APP', $_APP);
            }


			//AUTHENTICATE END <--
		}

	}
} 