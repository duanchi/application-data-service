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
class TestController extends Yaf\Controller_Abstract {
	
	public function indexAction($_action = NULL) {
		return $this->$_action();
	}

	public function requestAction() {
		var_dump($this->getRequest());
		return FALSE;
	}

	public function modelAction() {
		$_model_handler = new \Resource\NumberModel();
		var_dump($_model_handler->put());
		return FALSE;
	}

	public function envAction() {
		phpinfo();

		return FALSE;
	}

	public function rpcAction() {
		\CORE\Rpc::add_server(new TestServer(), NULL, 'Yar');
		\CORE\Rpc::handle();
		/*$server = new \CORE\Rpc\PHPRpc\Server();
		$server->add(new TestServer());
		$server->setCharset('UTF-8');
		$server->setDebugMode(FALSE);
		$server->start();
		*/

		return FALSE;
	}

	public function client() {
		$__REQUEST['b']    =  \IO\HTTP2::add_request(  [
			'uri'       =>  'http://api.ads.devel/env.php',
			'method'    =>  HTTP_GET,
			'host'      =>  '127.0.0.1'
		]);

		$__REQUEST['g']    =  \IO\HTTP2::add_request(  [
			'uri'       =>  'http://api.ads.devel/env.php',
			'method'    =>  HTTP_GET,
			'host'      =>  ''
		]);


		\Devel\Timespent::record('PRE-PROC');
		//if ($__REQUEST != FALSE) $__RESULT['data']   =   \IO\HTTP2::handle();

		$_node = 'table tr:eq(1):first td:btw(1,5):gt(2)';

		$tmp_node						= 	explode(':',str_replace(')',':',$_node));
		//preg_match_all('/(.+?)(:(eq|lt|gt|btw|first|last)\((\d+|\d+,\d+)\))*\S*/', 'table tr:eq(1):btw(1,5)', $_matches, PREG_SET_ORDER);
		//t($_matches);


		t($tmp_node);

		while(list($_key, $_node) = each($tmp_node)) {

			if (empty($_node) or $_node == '') ;

			elseif (strpos($_node, '(') === FALSE) {
				//execute normal
			} else {
				list($_option, $_parameter) = explode('(', $_node);
				t($_option,$_parameter);
			}

		}

		return FALSE;
	}

	public function etcAction() {
		var_dump(get_config('rpc'));
		return FALSE;
	}

    public function constant() {
        \CORE\STATUS::APP_NOT_DEFINED('700.1.1');
        return FALSE;
    }

    public function key() {
        $a =  null;
        $start = $this->get_microtime();
        for ($i = 0; $i<1000; $i++) {
            \CORE\KEY::set('test', $i, KEY_STATIC);
            $a = \CORE\KEY::get('test', KEY_STATIC);
        }
        $end = $this->get_microtime();

        var_dump($end - $start);

        $start = $this->get_microtime();
        for ($i = 0; $i<1000; $i++) {
            \Yaf\Registry::set('test', $i);
            $a = \Yaf\Registry::get('test');
        }
        $end = $this->get_microtime();

        var_dump(microtime());
        var_dump($end - $start);
        return FALSE;
    }

    private function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}
