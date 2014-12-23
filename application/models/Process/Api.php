<?php
namespace Process;

class ApiModel {

	/**
	 * Function get
	 * @param string $_scope
	 * @param string $_interface
	 * @return Ambigous <Array api_struct, boolean false>
	 */
	public static function get($_service = NULL, $_method = NULL, $_http_method = HTTP_GET) {
		$_result = FALSE;

		if ($_service != NULL && $_method != NULL) {
			$_result = parse_config(API_CONF_FILE_PATH);
		}

		if ($_result != NULL && isset($_result[$_service.'/'.$_method.':'.$_http_method])) {
			$_result = $_result[$_service.'/'.$_method.':'.$_http_method];
		} else {
			$_result = FALSE;
		}

		return $_result;
	}

	/**
	 * Function process
	 * @param $_api
	 * @param array $_parameters
	 * @return bool
	 */
	public static function process($_api, $_parameters = []) {
		return \CORE\Rpc::call()->$_api['proc_method']($_parameters, $_api);
	}

	/**
	 * parse & check $_parameters
	 * @param $_param_conf
	 * @param $_parameters
	 * @return array
	 */
	public static function parse_parameters($_param_conf, $_parameters) {
		$_result = FALSE;

		if (!empty($_param_conf)) {
			$_param_conf = explode(';', $_param_conf);

			if (count($_param_conf) != 0) {
				foreach($_param_conf as $v) {
					// 分解接口限制描述
					$_cell = explode('|', $v);
					$_cell_2 = (isset($_cell[1]) && $_cell[1] == 'HTTP_POST' ? 'HTTP_POST' : 'HTTP_GET');
					$_cell = explode(':', $_cell[0]);
					$_cell[2] = $_cell_2;
					$_cell[1] = (isset($_cell[1]) && $_cell[1] == '?' ? '?' : NULL);

					if ($_cell[2] == 'HTTP_GET') {
						if ($_cell[1] == '?' && !isset($_parameters['get'][$_cell[0]])) {

							$_result[$_cell[0]] = NULL;

						} elseif ($_cell[1] == NULL && !isset($_parameters['get'][$_cell[0]])) {

							//THROW EXCEPTION;

						} else {

							$_result[$_cell[0]] = $_parameters['get'][$_cell[0]];

						}
					} else {
						if ($_cell[1] == '?' && !isset($_parameters['post'][$_cell[0]])) {

							$_result[$_cell[0]] = NULL;

						} elseif ($_cell[1] == NULL && !isset($_parameters['post'][$_cell[0]])) {

							//THROW EXCEPTION;

						} else {

							$_result[$_cell[0]] = $_parameters['post'][$_cell[0]];

						}
					}
				}
			} else {
				$_result = [];
			}
		}

		return $_result;
	}

	public static function package($_data = NULL) {
		return $_data;
	}
}