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

    public function _initAutoload() {

        spl_autoload_register(function ($class_name) {
            \Yaf\Loader::import(\Yaf\Loader::getInstance()->getLibraryPath() . '/' . str_replace('\\', '/', $class_name) . '.php');
        });

    }

    public function _initConfig() {
		$config = Yaf\Application::app()->getConfig();
		Yaf\Registry::set('config', $config);
	}
	
	public function _initRoute(Yaf\Dispatcher $dispatcher) {
		$dispatcher->getRouter()->addConfig(Yaf\Registry::get('config')->routes);
	}

    public function _initFunction(Yaf\Dispatcher $dispatcher) {
        //初始化自定义全局函数
        //$this->_import('Function');
    }

    public function _initMemorySet(Yaf\Dispatcher $dispatcher) {
        //初始化预定于变量
        Yaf\Registry::set(      '__REQUEST', NULL );
        Yaf\Registry::set('__IS_AUTHORIZED', FALSE);
        Yaf\Registry::set(          '__APP', FALSE);
        Yaf\Registry::set(         '__CONF', FALSE);
        Yaf\Registry::set(     '__TMP_DATA', NULL );
        Yaf\Registry::set(     '__RAW_DATA', NULL );
        Yaf\Registry::set(     '__RESPONSE', NULL );
    }

    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        //注册插件
        $dispatcher->registerPlugin(new EnvPlugin());
        $dispatcher->registerPlugin(new ConstPlugin());
        $dispatcher->registerPlugin(new InitPlugin());
        $dispatcher->registerPlugin(new SecurityPlugin());

        $dispatcher->registerPlugin(new DevelPlugin());
    }

    public function _initHooks(Yaf\Dispatcher $dispatcher) {
        //注册Hooks
        $dispatcher->registerPlugin(new \Hook\RequestPlugin());
        $dispatcher->registerPlugin(new \Hook\AuthenticatePlugin());
        $dispatcher->registerPlugin(new \Hook\ParseConfigPlugin());
        $dispatcher->registerPlugin(new \Hook\FetchDataPlugin());
        //$dispatcher->registerPlugin(new \Hook\ExtraDataPlugin());
        $dispatcher->registerPlugin(new \Hook\ResponsePlugin());
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

		$file_path      =   \Yaf\Registry::get('config')->get('application')->library . DIRECTORY_SEPARATOR . $file_path;

        if (file_exists($file_path)) {
            $file_list  =   array_merge($file_list, glob($file_path . DIRECTORY_SEPARATOR . '*.php'));
        }

		foreach($file_list as $v) \Yaf\Loader::import($v);
	}


    private function _autoload($class_name) {

    }
}
