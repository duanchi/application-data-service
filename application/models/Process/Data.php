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

        $__RESULT                       =   [
                                                'request'   =>  [],
                                                'raw-data'  =>  [],
                                                'data'      =>  []
                                            ];
        $_data_type                     =   isset($_conf['data']['type'])
                                            ?
                                            $_conf['data']['type'] : ADS_TYPE_STREAM;

        //ETC DATAS
        $__RESULT['request']            =   [
            'id'    =>  $_request['id'],
            'uri'   =>  $_conf['request']['uri'],
        ];

        if ($__RESULT['request']['uri']['match-type'] == ADS_ROLE_REGEX)
            $__RESULT['request']['uri']['map']          =   $_tmp_data['uri-regex'];


        $__RESULT['raw-data']           =   $_raw_data;

        if (isset($_conf['data'])) {
            switch($_data_type) {
                case ADS_TYPE_HTML:
                    if (isset($_conf['data']['node']))
                        $__RESULT['data']=   self::parse_html_data($_raw_data['body'], $_conf);
                    break;

                case ADS_TYPE_JSON:
                    $__RESULT['data']   =   json_decode($_raw_data['body'], TRUE);
                    break;


                case ADS_TYPE_STREAM:
                default:

                    break;
            }
        }

        return $__RESULT;
    }

    public  static function package_response($_response_data, $_request, $_conf) {

        $__RESULT                       =   [
                                                'DATA'          =>  $_response_data['data'],
                                                'CONTENT-TYPE'  =>  $_request['content-type'],
                                                'CALLBACK'      =>  isset($_request['ads_parameters']['callback'])
                                                                    ?
                                                                    $_request['ads_parameters']['callback'] : NULL,
                                                'HEADER'        =>  []
                                            ];

        $__RESULT['HEADER']             =   [
                                                'Request-Id'    =>  $_request['id'],
                                                'Content-Type'  =>  $__RESULT['CONTENT-TYPE'],
        ];

        switch($_conf['role']['request']['scheme']) {

            case ADS_SCHEME_HTTP:

                if (!isset($_response_data['raw-data']['data']['header']['set-cookie'])) break;

                $_header_set = 1;
                if (isset($_conf['role']['data']['header']) and !empty($_conf['role']['data']['header'])) {
                    (strpos($_conf['role']['data']['header'], ADS_FIELD_DATA)   === FALSE) ?    $_header_set -= 1 : NULL;
                    (strpos($_conf['role']['data']['header'], ADS_FIELD_HEADER) !== FALSE) ?    $_header_set += 2 : NULL;
                    (strpos($_conf['role']['data']['header'], ADS_FIELD_RAW)    !== FALSE) ?    $_header_set += 4 : NULL;
                }

                $_tmp_cookie            =   [];

                foreach ($_response_data['raw-data']['data']['header']['set-cookie'] as $_cookie_node)
                    $_tmp_cookie[]      =   (new \http\Cookie($_cookie_node))->toArray();

                if ($_header_set & 1) {
                    $__RESULT['DATA']['COOKIE']         =   $_tmp_cookie;
                }

                if ($_header_set & 2) {
                    $_raw_cookie        =   str_split(
                                                        encrypt(
                                                            ENCRYPT_BASE64,
                                                            \msgpack_pack($__RESULT['DATA']['COOKIE'])
                                                        ),
                                                        4 * 4096
                                                    );

                    foreach($_raw_cookie as $_key => $_cookie_node) {
                        $_cookie_handle =   new \http\Cookie();
                        $_cookie_handle->setCookie('ADS-PROXY-'.$_key, $_cookie_node);

                        t($_cookie_handle->toArray());
                    }
                    $__RESULT['HEADER']['Set-Cookie']   =   str_split(
                                                                        encrypt(
                                                                                    ENCRYPT_BASE64,
                                                                                    \msgpack_pack($__RESULT['DATA']['COOKIE'])
                                                                               ),
                                                                        4 * 4096
                                                                     );
                }

                if ($_header_set & 4) {
                    !isset($__RESULT['HEADER']['Set-Cookie']) ? $__RESULT['HEADER']['Set-Cookie']   = [] : NULL;
                    $__RESULT['HEADER']['Set-Cookie']   =   array_merge($__RESULT['HEADER']['Set-Cookie'], $_response_data['raw-data']['data']['header']['set-cookie']);
                }

                break;

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