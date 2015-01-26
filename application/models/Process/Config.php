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
class ConfigModel {

    private static $__TMP_VAR                   =   [];
	
	public static function parse($_app, $_request) {
        return self::get_config($_app, $_request);
    }

    private static function get_config($_app, $_request) {
        $__RESULT                               =   FALSE;
        $_conf_directory                        =   ADS_APPS_CONFIG . DIRECTORY_SEPARATOR . $_app->appkey . DIRECTORY_SEPARATOR;
        $_licence_config                        =   get_yaf_config($_conf_directory . 'licence.ini');


        if (    isset($_licence_config->licence->key)
                and
                $_licence_config->licence->key == $_app->licence
            ) {

            $_h_conf                            =   get_yaf_config($_conf_directory . 'conf.h.ini');

            if (isset($_h_conf->etc->constant))
                foreach ($_h_conf->etc->constant as $_key => $_constant) define($_key, $_constant);

            $_conf                              =   get_yaf_config($_conf_directory . 'conf.ini')->toArray();


            //PARSE REQUEST ROLES
            $_role                              =   self::get_request_config($_conf['roles'], $_request);
            unset($_conf['roles']);

            if ($_role !== FALSE) {
                $_conf['role']                  =   $_role;

                //PARSE RESPONSE-DATA
                if (isset($_conf['role']['data']))
                    $_conf['role']['data']      =   self::parse_response_data_conf($_conf['role']['data']);
            }

            //PARSE ETC->HOSTS
            if (isset($_h_conf->etc->hosts))
                $_conf['etc']['hosts']          =   $_h_conf->etc->hosts->toArray();
            else ;//@todo THROW NO HOSTS ERROR


            $__RESULT                           =   $_conf;
        }

        return $__RESULT;
    }

    private static function get_request_config($_roles, $_request) {

        $__RESULT                               =   FALSE;
        $__TMP_DATA                             =   \Yaf\Registry::get('__TMP_DATA');
        //FETCH CONF WITH URI OR KEY
        foreach($_roles as $_tmp_value) {

            if (
                $_request['key'] == NULL
                and
                isset($_tmp_value['request']['type'])
                and
                $_tmp_value['request']['type'] == ADS_ROLE_REGEX
                and
                preg_match($_tmp_value['request']['uri'], $_request['uri'], $__matches)
            ) {
                //PUT MATCHES TO TMP DATA
                if (isset($_tmp_value['request']['map'])) {

                    foreach ($__matches as $_key => $__match) {

                        if (isset($_tmp_value['request']['map'][$_key])) {

                            $__TMP_DATA['$' . $_tmp_value['request']['map'][$_key]]   =   $__TMP_DATA['uri-regex'][$_tmp_value['request']['map'][$_key]]  =   $__match;


                        } else $__TMP_DATA['$' . $_key]                               =   $__TMP_DATA['uri-regex'][$_key]                                 =   $__match;

                    }

                }

                \Yaf\Registry::set('__TMP_DATA', $__TMP_DATA);
                unset($__TMP_DATA['uri-regex']);

                $_tmp_value['request']['uri']   =   $_request['uri'];

            } elseif (
                $_request['key'] == NULL
                and
                $_tmp_value['request']['uri'] == $_request['uri']
            ) {

                $_tmp_value['request']['type']  =   ADS_ROLE_SIMPLE;

            } elseif (
                $_request['key'] != NULL
                and
                $_tmp_value['key'] == $_request['key']
            ) {

                $_tmp_value['request']['type']  =   ADS_ROLE_KEY;

            } else goto no_match_role;

            $__RESULT                           =   $_tmp_value;
            break;

            no_match_role:

        }

        if ($__RESULT !== FALSE) {
            $__RESULT['request']['uri']         =   [
                                                        'raw'           =>  $__RESULT['request']['uri'],
                                                        'match-type'    =>  $__RESULT['request']['type']
                                                    ];

            if (filter_var($__RESULT['request']['uri']['raw'], FILTER_VALIDATE_URL)) {
                $__RESULT['request']['uri']     =   array_merge(
                                                                    $__RESULT['request']['uri'],
                                                                    parse_url($__RESULT['request']['uri']['raw'])
                                                                );
            }

            if (!empty($__TMP_DATA))
                $__RESULT['data']['node']       =   self::replace_tmp_var($__RESULT['data']['node'], $__TMP_DATA);
        }

        return $__RESULT;
    }

    private static function parse_response_data_conf ($_raw_data_conf) {
        $__RESULT                               =   $_raw_data_conf;

        !isset($__RESULT['type']) ? $__RESULT['type']   =   ADS_TYPE_STREAM : FALSE;

        return $__RESULT;
    }

    private static function replace_tmp_var ($_subjects, $_replaces) {

        $_find                                  =   array_keys($_replaces);
        $_value                                 =   array_values($_replaces);
        $__RESULT                               =   str_replace($_find, $_value, $_subjects);

        return $__RESULT;
    }
}