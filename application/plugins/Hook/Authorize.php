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

class AuthorizePlugin extends \Yaf\Plugin_Abstract {

	public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {

		if ($request->controller == 'Api' && \Core\KEY::get('_IS_AUTHORIZED')) {

            $_REQUEST = \Core\KEY::get('_REQUEST');
            $_APP     = \Core\KEY::get('_APP');

            //AUTHORIZE START -->
            //授权, with service, method, token
            if ($_APP != FALSE && \Process\AuthorizeModel::authorize(
                    $_REQUEST['api']['service'],
                    $_REQUEST['api']['method'],
                    $_APP['role']
                )) {
                //授权成功
                \Core\KEY::set('_IS_AUTHORIZED', TRUE);
            } else {
                throw new \Exception('PERMISSION_DENIED');
            }
            //AUTHORIZE END <--
		}

	}
} 