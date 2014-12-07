<?php
/**
 * Created by PhpStorm.
 * User: shijy33
 * Date: 14-7-11
 * Time: 15:34
 */

function get_yaf_config($_config_file) {
	return new \Yaf\Config\Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . $_config_file, \Yaf\Application::app()->environ());
}