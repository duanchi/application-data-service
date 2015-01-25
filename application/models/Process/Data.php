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
class DataModel {

    public  static function parse_data($_raw_data, $_conf, $_tmp_data) {

        $__RESULT               =   FALSE;
        $_data_type             =   isset($_conf['data']['type'])
                                    ?
                                    $_conf['data']['type'] : ADS_TYPE_STREAM;

        switch($_data_type) {
            case ADS_TYPE_HTML:
                $__RESULT['data']       =   self::parse_html_data($_raw_data['body'], $_conf);
                break;

            case ADS_TYPE_JSON:

                break;


            case ADS_TYPE_STREAM:
            default:

                break;
        }

        //ETC DATAS
        $__RESULT['uri']                =   $_conf['request']['uri'];

        if ($__RESULT['uri']['match-type'] == ADS_ROLE_REGEX) {
            $__RESULT['uri']['map']     =   $_tmp_data['uri-regex'];
        }


        return $__RESULT;
    }

    private static function parse_html_data($_raw_data, $_conf = NULL) {

        $__RESULT                       =   NULL;
        $_data_handle                   =   new \Data\HtmlParser($_raw_data);

        foreach($_conf['data']['node'] as $_key => $_node) {
            $__RESULT[$_key]            =   $_data_handle->find($_node);
        }

        t($__RESULT);
        //$_ret = $_data_handle->find();
    }
}