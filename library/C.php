<?php

/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/10/22
 * Time: 下午3:44
 */
class C
{
	static public function GVAR() {
		if (1 == func_num_args()) return \Yaf\Registry::get(func_get_arg(0));
		else \Yaf\Registry::set(func_get_arg(0), func_get_arg(1));
	}

	static public function IS_ADS_ROUTE() {
		$__request          =   \Yaf\Application::app()->getDispatcher()->getRequest();

		if (
			'index' == strtolower($__request->module)
			&&
			'index' == strtolower($__request->controller)
			&&
			'index' == strtolower($__request->action)
		) return TRUE;
		else return FALSE;
	}
}