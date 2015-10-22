<?php
/**
 * @name Bootstrap
 * @author duanChi <http://weibo.com/shijingye>
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf\Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract{

    public function _initConfig() {

        \CONF::set_environment(\Yaf\Application::app()->environ(), APPLICATION_KEY);
        $config = \CONF::get('application');
		Yaf\Registry::set('config', $config);
	}

    public function _initAutoload() {

        \Yaf\Loader::getInstance()->registerLocalNamespace(explode(',', \CONF::get('application.local_namespace')));

        spl_autoload_register(function ($class_name) {
            \Yaf\Loader::import(\Yaf\Loader::getInstance()->getLibraryPath(FALSE) . DIRECTORY_SEPARATOR . str_replace('\\', '/', $class_name) . '.php');
        });

    }

	public function _initRoute(Yaf\Dispatcher $dispatcher) {
		$dispatcher->getRouter()->addConfig(\CONF::get('application.routes'));
	}

    public function _initFunction(Yaf\Dispatcher $dispatcher) {
        //初始化自定义全局函数
        $this->_import('Function');
    }

    public function _initGlobalVariable(Yaf\Dispatcher $dispatcher) {
        //初始化预定于变量
        C::GVAR(        '__REQUEST', []);
        C::GVAR(  '__IS_AUTHORIZED', FALSE);
        C::GVAR(            '__APP', []);
        C::GVAR(         '__CONFIG', []);
        C::GVAR(       '__RAW_DATA', []);
        C::GVAR(       '__RESPONSE', []);
    }

    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        //注册插件
        //$dispatcher->registerPlugin(new RouterPlugin());
        $dispatcher->registerPlugin(new ConstPlugin());
        $dispatcher->registerPlugin(new SecurityPlugin());
        $dispatcher->registerPlugin(new DevelPlugin());
    }

    public function _initHooks(Yaf\Dispatcher $dispatcher) {
        //注册Hooks
        HOOKPlugin::register(
            [
                'Request'
            ],
            $dispatcher
        );
        //$dispatcher->registerPlugin(new \Hook\RequestPlugin());
        //$dispatcher->registerPlugin(new \Hook\AuthenticatePlugin());
        //$dispatcher->registerPlugin(new \Hook\ParseConfigPlugin());
        //$dispatcher->registerPlugin(new \Hook\FetchDataPlugin());
        //$dispatcher->registerPlugin(new \Hook\ExtraDataPlugin());
        //$dispatcher->registerPlugin(new \Hook\ResponsePlugin());
        //$dispatcher->registerPlugin(new \Hook\PostEventPlugin());
    }
	
	/**
	 * @name	import
	 * 加载自定义包
	 * @version	1.0.0
	 * @since	2012-03-26
	 * @param	unknown_type $type
	 */
    private function _import ($file_path) {

        $file_list      =   [];

        $file_path      =   \Yaf\Loader::getInstance()->getLibraryPath(TRUE) . DIRECTORY_SEPARATOR . $file_path;

        if (file_exists($file_path)) {
            $file_list  =   glob($file_path . DIRECTORY_SEPARATOR . '*.php');
        }

        $file_path      =   \Yaf\Loader::getInstance()->getLibraryPath(FALSE) . DIRECTORY_SEPARATOR . $file_path;

        if (file_exists($file_path)) {
            $file_list  =   array_merge($file_list, glob($file_path . DIRECTORY_SEPARATOR . '*.php'));
        }

        foreach($file_list as $v) \Yaf\Loader::import($v);
    }
}
