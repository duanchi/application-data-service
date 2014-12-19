<?php
/**
 * File    libraries\VOP\Authorize.php
 * Desc    认证类实现文件
 * Manual  svn://svn.vop.com/api/manual/VOP/Authorize
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-10-29
 * Time    15:36
 */

namespace Process;
/**
 * Class Authorize
 * @package Process
 */
class AuthorizeModel {
	
	/**
	 * Function authenticate
	 * @param string $_token
	 * @param string $_ip
	 * @return Ambigous <boolean, Ambigous, multitype:>
	 */
	public static function authenticate($_token = NULL, $_ip = NULL, $_client_id = NULL, $_client_token = NULL) {
		$_result = FALSE;

		$_result = self::get_app(NULL, NULL, $_token);

		if ($_result) {

			if (API_IP_BIND == TRUE) {
				if (!self::ip_match($_result->appkey, $_ip)) {
                    $_result = FALSE;
                    throw new \Exception('INVAILED_REQUEST_IP');
                }
			}
		}

		return $_result;
	}
	
	/**
	 * 
	 * @param string $_scope
	 * @param string $_interface
	 * @param string $_conf
	 * @return boolean
	 */
	public static function authorize($_service = NULL, $_method = NULL, $_conf = NULL) {
		$_result = FALSE;
		
		if ($_conf != NULL) $_conf = explode(';', $_conf);
		
		foreach ($_conf as $v) {
            /* 分解接口限制描述 */
            $_cell = explode('|', $v);
            $_cell_2 = (isset($_cell[1]) ? $_cell[1] : NULL);
			$_cell = explode(':', $_cell[0]);
            $_cell[2] = $_cell_2;

			//初始化$_cell的其他选项$_cell[0]:接口,$_cell[1]:版本,$_cell[2]:次数限制,$_version:待比较的版本号
			$_cell[1] = (isset($_cell[1]) ? $_cell[1] : NULL);
			$_cell[2] = (isset($_cell[2]) ? intval($_cell[2]) : 0);


			//不需要接口版本校验
			/*if (preg_match('/'.$_cell[0].'/', $_service.'/'.$_method) && self::_version_compare($_version, $_cell[1])) {
				$_result = TRUE;
				break;
			}*/

			if (preg_match('/'.$_cell[0].'/', $_service.'/'.$_method)) {
				$_result = TRUE;
				break;
			}
		}

		return $_result;
	}
	
	/**
	 * 
	 * @param unknown $_appkey
	 * @param unknown $_appsecret
	 * @return Ambigous <boolean, Ambigous, multitype:>
	 */
	protected static function get_app($_appkey = NULL, $_appsecret = NULL, $_token = NULL) {
		$_result = FALSE;

		//如果是token,先找到token和 appkey的对应
		if ($_appkey == NULL && $_token != NULL) {
			$_tmp_token_config = get_yaf_config(ADS_APPS_SECRET);
			if (!empty($_tmp_token_config) && isset($_tmp_token_config[$_token])) {
                $_result = $_tmp_token_config[$_token];
			}
		}
		
		return $_result;
	}
	
	/**
	 * 
	 * @param unknown $_app_obj
	 * @param unknown $_ip
	 * @return boolean
	 */
	protected static function ip_match($_app_obj, $_ip) {
		$_result = FALSE;
		
		return $_result;
	}
	
	/**
	 * 
	 * @param unknown $_appkey
	 * @return boolean
	 */
	protected static function check_disabled($_appkey) {
		$_result = FALSE;
		if (API_DISABLED_APPS != '' && array_search($_appkey, explode(',', API_DISABLED_APPS)) != NULL) {
			$_result = TRUE;
		}
		
		return $_result;
	}

	/**
	 *
	 * @param $_version
	 * @param $_base_verison
	 * @return bool
	 */
	protected static function _version_compare($_version, $_base_verison) {
		$_result = FALSE;

		if ($_base_verison == NULL) {

			$_result = TRUE;

		} else {
			$_sign = str_replace(' ', '', $_base_verison);
			$_length = strlen($_base_verison);
			$_base_verison = '';

			for($i = 0; $i < $_length; $i++) {
				$_cur = substr($_sign, -1,1);
				$_sign = rtrim($_cur);
				if (ord($_cur) >= 48 && ord($_cur) <= 57 ) {
					$_base_verison = $_cur . $_base_verison;
					break;
				}
			}

			switch($_sign) {
				case '>':
				case 'gt':
					if ($_base_verison > $_version) $_result = TRUE;
					break;

				case '>=':
				case 'gte':
					if ($_base_verison >= $_version) $_result = TRUE;
					break;

				case '<':
				case 'lt':
					if ($_base_verison < $_version) $_result = TRUE;
					break;

				case '<=':
				case 'lte':
					if ($_base_verison <= $_version) $_result = TRUE;
					break;

				case '=':
				case 'eq':
				case '':
				default:
					if ($_base_verison == $_version) $_result = TRUE;
					break;
			}
		}

		return $_result;
	}
}