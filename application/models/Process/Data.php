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
                        $__RESULT['data']=   self::parse_html_data($_raw_data['data']['body'], $_conf);
                    break;

                case ADS_TYPE_JSON:
                    $__RESULT['data']   =   json_decode($_raw_data['data']['body'], TRUE);
                    break;


                case ADS_TYPE_STREAM:
                default:

                    break;
            }
        }

        return $__RESULT;
    }

    public  static function package_response($_response_data, $_request, $_conf) {

        $__RESULT                                           =   [
                                                                    'DATA'          =>  $_response_data['data'],
                                                                    'CONTENT-TYPE'  =>  $_request['content-type'],
                                                                    'CALLBACK'      =>  isset($_request['ads_parameters']['callback'])
                                                                                        ?
                                                                                        $_request['ads_parameters']['callback'] : NULL,
                                                                    'HEADER'        =>  [
                                                                                            'Request-Id'    =>  $_request['id'],
                                                                                            'Content-Type'  =>  $_request['content-type'],
                                                                                        ]
                                                                ];

        $__RESULT['DATA']['request']                        =   $_response_data['request'];

        switch($_conf['role']['request']['scheme']) {

            case ADS_SCHEME_HTTP:

                if (!isset($_response_data['raw-data']['data']['header']['set-cookie'])) break;

                $__tmp_cookie                               =   self::parse_cookie_data(
                                                                                            $_response_data['raw-data']['data']['header']['set-cookie'],
                                                                                            isset($_conf['role']['data']['header']) ? $_conf['role']['data']['header'] : NULL,
                                                                                            $_response_data['request']['uri']['host']
                                                                                        );

                $__tmp_cookie['DATA']   != NULL ? $__RESULT['DATA']['cookie']       =   $__tmp_cookie['DATA']   : FALSE;
                $__tmp_cookie['HEADER'] != NULL ? $__RESULT['HEADER']['Set-Cookie'] =   $__tmp_cookie['HEADER'] : FALSE;

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

    private static function parse_cookie_data($_raw_cookies, $_scope = NULL, $_default_host) {

        $_header_set                                =   1;
        $_tmp_cookie                                =   [];
        $__RESULT                                   =   [
                                                            'DATA'      =>  NULL,
                                                            'HEADER'    =>  []
                                                        ];

        if (
            $_scope != NULL
            or
            !empty($_scope)
        ) {
            (strpos($_scope, ADS_FIELD_DATA)   === FALSE) ?     $_header_set -= 1 : NULL;
            (strpos($_scope, ADS_FIELD_HEADER) !== FALSE) ?     $_header_set += 2 : NULL;
            (strpos($_scope, ADS_FIELD_RAW)    !== FALSE) ?     $_header_set += 4 : NULL;
        }

        foreach ($_raw_cookies as $_cookie_node) {
            $_tmp_node                              =   new \http\Cookie($_cookie_node);
            $_tmp_node->getDomain() === NULL ? $_tmp_node->setDomain($_default_host) : FALSE;
            $_tmp_cookie[]                          =   $_tmp_node->toArray();
        }

        if ($_header_set & 1) {
            $__RESULT['DATA']                       =   $_tmp_cookie;
        }

        if ($_header_set & 2) {
            $_raw_cookie                            =   str_split(
                encrypt(
                    ENCRYPT_BASE64,
                    \msgpack_pack($__RESULT['DATA'])
                ),
                4 * 4096
            );

            foreach($_raw_cookie as $_key => $_cookie_node) {
                $__RESULT['HEADER'][]               =   (new \http\Cookie())
                                                            ->setCookie('ADS-PROXY-'.$_key, $_cookie_node)
                                                            ->toArray();
            }

        }

        if ($_header_set & 4) {
            !isset($__RESULT['HEADER']) ? $__RESULT['HEADER']   = [] : NULL;
            $__RESULT['HEADER']       =   array_merge($__RESULT['HEADER'], $_tmp_cookie);
        }

        return $__RESULT;
    }
}