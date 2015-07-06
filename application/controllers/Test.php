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

	public function env() {
		new \Api\Api();
		phpinfo();

		return FALSE;
	}

    public function sms() {
        $array_1    =   parse_ini_file(APPLICATION_PATH . '/tmp/1.txt');

        $_ntf_array =   [];
        $_tmp_arr   =   [];
        foreach ($array_1 as $arr) {
            $_tmp_arr = explode(',', iconv('GB2312', 'UTF-8', $arr));

            $_ntf_array[$_tmp_arr[0]][$_tmp_arr[1]] =   $_tmp_arr[2];
        }

        $array_2    =   parse_ini_file(APPLICATION_PATH . '/tmp/2.txt');
        $_sms_array =   [];

        foreach ($array_2 as $key => $arr) {
            $_tmp_arr   = explode(',', $arr);
            $key        =   iconv('GBK', 'UTF-8', $key);

            $_sms_array[$key]     =   $_tmp_arr;
        }

        $_sum_array =   [];

        foreach ($_sms_array as $key => $arr) {
            $_sum_array[$key]   =   [];

            foreach ($arr as $sub_key => $sub_arr) {
                $_sum_array[$key][$sub_key] =   ($sub_arr + $_ntf_array[$key][$sub_key]) * 3;
            }
        }




        //----------------------------
        file_put_contents(APPLICATION_PATH . '/tmp/out_mt.csv',iconv('UTF-8', 'GB2312', "2015-01-01,00点,01点,02点,03点,04点,05点,06点,07点,08点,08点,10点,11点,12点,13点,14点,15点,16点,17点,18点,19点,20点,21点,22点,23点\r\n"), FILE_APPEND);

        foreach ($_sum_array as $key => $arr) {
            file_put_contents(APPLICATION_PATH . '/tmp/out_mt.csv', iconv('UTF-8', 'GB2312', $key) . ',' . implode(',', $arr) . "\r\n", FILE_APPEND);
        }
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

		$_node_stack						= 	explode(' ', str_replace(')', '', $_node));
		//preg_match_all('/(.+?)(:(eq|lt|gt|btw|first|last)\((\d+|\d+,\d+)\))*\S*/', 'table tr:eq(1):btw(1,5)', $_matches, PREG_SET_ORDER);
		//t($_matches);


		t($_node_stack);
		$_find_expression				=	'';
		do {
			$_node = explode(':', current($_node_stack));

			$_find_expression		.=	' ' . array_shift($_node);
			t($_find_expression);
			if (!empty($_node)) {
				//execute normal handle
				$_data_handle			=	[];
				$_data_handle			=	$this->parse_senior_selection_node($_data_handle, $_node);
				$_find_expression		=	'';


			}

		} while(next($_node_stack));

		return FALSE;
	}


	public function proxy() {

		$_data                          =   file_get_contents('php://input');

		file_put_contents(APPLICATION_PATH . '/cache/proxy.log',$_data ."\r\n\r\n", FILE_APPEND);

		return FALSE;
	}
	private function parse_senior_selection_node($_data_handle, $_node) {

		$_result			=	$_data_handle;
		$_node_length 		= 	count($_result);

		foreach ($_node as $_key => $_node_x) {
			$_result		=	$this->parse_matched_option($_result, explode('(', strtolower($_node_x)), $_node_length);
		}

		return $_result;
	}

	private function parse_matched_option($_data_handle, array $_node, $_node_length) {
		
		$_result			=	[];

		switch($_node[0]) {
			case 'gt':
				if ($_node_length < $_node[1]) break;
				$_result	=	array_slice($_data_handle, $_node[1], $_node_length - $_node[1]);
				break;

			case 'lt':
				if ($_node_length < $_node[1]) break;
				$_result	=	array_slice($_data_handle, 0, $_node[1]);
				break;

			case 'between':
				$_node[1]	=	explode(',', $_node[1]);
				if (!isset($_node[1][1]) or $_node_length < $_node[1][1]) break;
				$_result	=	array_slice($_data_handle, $_node[1][0], $_node_length - $_node[1][1]);
				break;

			case 'first':
				if ($_node_length < $_node[1]) break;
				$_result[]	=	$_data_handle[0];
				break;

			case 'last':
				if ($_node_length < $_node[1]) break;
				$_result[]	=	array_pop($_data_handle);
				break;

			case 'eq':
				if ($_node_length < $_node[1]) break;
				$_result[]	=	$_data_handle[$_node[1]];
				break;
		}

		return $_result;
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
