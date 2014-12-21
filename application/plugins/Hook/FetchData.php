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

class FetchDataPlugin extends \Yaf\Plugin_Abstract {

	public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {

		if ($request->controller == 'Ads' && \Yaf\Registry::get('__IS_AUTHORIZED') == TRUE) {

            $__CONF		= \Yaf\Registry::get('__CONF');
			//FETCH DATA START -->
            //FETCH RAW DATA USE CURL
			$__RAW_DATA	= \Process\DataModel::fetch_raw_data($__CONF);

			$__DATA		= $__RAW_DATA;
            \Yaf\Registry::set('__DATA', $__DATA);


			//FETCH CONFIG DATA
			//FETCH DATA END <--
		}

	}
}