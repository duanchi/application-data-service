<?php
class RouterPlugin extends Yaf\Plugin_Abstract {

	function __construct() {
	}

	public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
		$__tmp_module           =   explode(\CONF::get('application.host_suffix'), $_SERVER['HTTP_HOST'], 2);

		if (2 == count($__tmp_module)) {
			$request->setModuleName(ucfirst($__tmp_module[0]));
		}
		else {
			throw new \Exception();
		}
	}
	
	public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {

	}
	
	public function preDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function postDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function preResponse(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	
	}
}