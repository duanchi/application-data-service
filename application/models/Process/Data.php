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

    public  static function parse_data($_raw_data, $_request, $_conf, $_tmp_data) {

        $__RESULT               =   FALSE;
        $_data_type             =   isset($_conf['data']['type'])
                                    ?
                                    $_conf['data']['type'] : ADS_TYPE_STREAM;

        //ETC DATAS
        $__RESULT['request']            =   [
            'id'    =>  $_request['id'],
            'uri'   =>  $_conf['request']['uri'],
        ];

        if ($__RESULT['request']['uri']['match-type'] == ADS_ROLE_REGEX)
            $__RESULT['request']['uri']['map']          =   $_tmp_data['uri-regex'];

        switch($_data_type) {
            case ADS_TYPE_HTML:
                $__RESULT['data']       =   self::parse_html_data($_raw_data['body'], $_conf);
                break;

            case ADS_TYPE_JSON:
                $__RESULT['data']       =   json_decode($_raw_data['body'], TRUE);
                break;


            case ADS_TYPE_STREAM:
            default:

                break;
        }

        return $__RESULT;
    }

    private static function parse_html_data($_raw_data, $_conf = NULL) {

        $__RESULT                       =   NULL;
        $_tmp_data                      =   NULL;
        $_data_handle                   =   new \Data\HtmlParser($_raw_data);

        foreach($_conf['data']['node'] as $_key => $_node) {
            $_tmp_data                  =   $_data_handle->find($_node);

            if (!empty($_tmp_data)) foreach ($_tmp_data as $_data) {
                $__RESULT[$_key][]      =   [
                                                'value'   =>  $_data->getPlainText(),
                                                'attribute' =>  $_data->getAttr(),
                                            ];
            }
        }


        return $__RESULT;
    }
}