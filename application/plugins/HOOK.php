<?php

/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/10/22
 * Time: 下午3:26
 */
class HOOKPlugin
{
	static public function register(array $_hooks_list, $_dispatcher) {

		while($__hooks = array_shift($_hooks_list)) {
			$__hooks    =   '\\Hooks\\' .$__hooks . 'Plugin';
			$_dispatcher->registerPlugin(new $__hooks());
		}
	}
}