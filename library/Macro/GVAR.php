<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/10/21
 * Time: 下午3:34
 */

namespace Macro;


class GVAR
{
	static public function get($_var) {
		return \Yaf\Registry::get($_var);
	}

	static public function set($_var, $_value) {
		return \Yaf\Registry::set($_var, $_value);
	}
}