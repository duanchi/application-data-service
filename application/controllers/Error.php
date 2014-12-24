<?php
/**
 * File    application\controllers\Error.php
 * Desc    异常控制模块
 * Manual  svn://svn.vop.com/api/manual/Controller/Error
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-10-29
 * Time    15:36
 */

/**
 * @name    ErrorController
 * @desc    错误控制器, 在发生未捕获的异常时刻被调用
 * @see     svn://svn.vop.com/api/manual/Controller/Error
 * @author  duanChi <http://weibo.com/shijingye>
 */
class ErrorController extends Yaf\Controller_Abstract {

	public function errorAction($exception) {
		//$_message = explode('|', $exception->getMessage(), 2);

		//$_error = \Api\MessageCode::get($_message[0]);

		var_dump($exception);
        //\CORE\RESPONSE::initialize($response, RESPONSE_TYPE_YAF);
        //\CORE\RESPONSE::set($_ECHO, RESPONSE_TYPE_BODY);
        //\CORE\RESPONSE::respond();

		//_fastcgi_finish_request();
		return FALSE;
	}

	public function emptyAction() {
		throw new \Exception('API_ROUTE_ERROR');
		return FALSE;
	}
}