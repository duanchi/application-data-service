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
use IO\HTTP2\Cookie;

/**
 * Class Authorize
 * @package Process
 */
class DataModel {

    public  static function parse_data($_raw_data, $_request, $_conf, $_tmp_data) {

        $__RESULT                                       =   [
                                                                'request'   =>  [],
                                                                'raw-data'  =>  [],
                                                                'data'      =>  []
                                                            ];
        $_data_type                                     =   isset($_conf['data']['type'])
                                                            ?
                                                            $_conf['data']['type'] : ADS_TYPE_STREAM;

        //ETC DATAS
        $__RESULT['request']                            =   [
                                                                'id'    =>  $_request['id'],
                                                                'uri'   =>  $_conf['request']['uri'],
                                                            ];

        if ($__RESULT['request']['uri']['match-type']  == ADS_ROLE_REGEX)
            $__RESULT['request']['uri']['map']          =   $_tmp_data['uri-regex'];


        $__RESULT['raw-data']                           =   $_raw_data;

        //GET CONTENT-TYPE
        $__RESULT['raw-data']['body']   =   self::transcoding($__RESULT['raw-data']['body'], $_raw_data['header'], $_conf);

        if (isset($_conf['data'])) {

            switch($_data_type) {

                case ADS_TYPE_HTML:
                    if (isset($_conf['data']['node'])) {
                        $__RESULT['data']               =   self::parse_html_data($__RESULT['raw-data']['body'], $_conf['data']);
                    }

                    break;

                case ADS_TYPE_JSON:
                    $__RESULT['data']                   =   json_decode($__RESULT['raw-data']['body'], TRUE);

                    break;

                case ADS_TYPE_MSGPACK:
                    $__RESULT['data']                   =   msgpack_unpack($__RESULT['raw-data']['body'], TRUE);
                    break;

                case ADS_TYPE_JSONP:

                    break;

                case ADS_TYPE_XML:

                    break;

                case ADS_TYPE_STREAM:
                case ADS_TYPE_PLAIN_TEXT:
                default:
                    $__RESULT['data']                   =   $__RESULT['raw-data']['body'];
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

                if (!isset($_response_data['raw-data']['header']['set-cookie'])) break;

                $__tmp_cookie                               =   self::parse_cookie_data(
                                                                                            $_response_data['raw-data']['header']['set-cookie'],
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

    private static function parse_html_data($_raw_data, $_conf = NULL, $_content_type = []) {

        $__RESULT                       =   NULL;
        $_tmp_data                      =   NULL;

        $_data_handle                   =   new \Data\HtmlParser($_raw_data);

        foreach($_conf['node'] as $_key => $_node) {
            $__RESULT[$_key]            =   self::parse_data_node($_data_handle, $_node);
        }


        return $__RESULT;
    }

    private static function parse_data_node($_data_handle, $_node) {

        $__RESULT                       =   FALSE;
        $_tmp_data                      =   [$_data_handle];

        /*if (preg_match_all('(.+?):(eq|lt|gt|btw|first|last)(\(\d+\))?\S*', $_node, $_matches, PREG_SET_ORDER) && $_matches) {
            t($_matches);

        } else {
            t(1);
            $_tmp_data                  =   $_data_handle->find($_node);
        }

        if (!empty($_tmp_data)) foreach ($_tmp_data as $_data) {
            $__RESULT[]                 =   [
                                                'value'   =>  $_data->getPlainText(),
                                                'attribute' =>  $_data->getAttr(),
                                            ];
        }*/
        \Devel\Timespent::record('GET-NODE1');
        $_node_stack					= 	explode(' ', str_replace(')', '', $_node));
        //preg_match_all('/(.+?)(:(eq|lt|gt|btw|first|last)\((\d+|\d+,\d+)\))*\S*/', 'table tr:eq(1):btw(1,5)', $_matches, PREG_SET_ORDER);
        //t($_matches);

        $_find_expression				=	'';
        do {
            $_node = explode(':', current($_node_stack));
            $_find_expression		   .=	' ' . array_shift($_node);

            if (!empty($_node)) {
                $_tmp_data			    =	self::parse_senior_selection_node($_tmp_data, $_find_expression, $_node);
                $_find_expression		=	'';
            }

        } while(next($_node_stack));

        if (!empty($_find_expression))
            $_tmp_data                  =   self::parse_normal_selection_node($_tmp_data, $_find_expression);

        \Devel\Timespent::record('GET-NODE2');
        if (!empty($_tmp_data)) foreach ($_tmp_data as $_data) {
            $__RESULT[]                 =   [
                'value'   =>  $_data->getPlainText(),
                'attribute' =>  $_data->getAttr(),
            ];
        }

        return $__RESULT;
    }

    private static function parse_normal_selection_node($_data_handle, $_node) {

        $_result                        =   [];

        foreach ($_data_handle as $_current_data_node) {
            $_tmp_result                =   $_current_data_node->find($_node);
            $_tmp_result != NULL
            ? $_result                  =   array_merge($_result, $_tmp_result) : FALSE;
        }

        return  $_result;
    }

    private static function parse_senior_selection_node($_data_handle, $_find_expression, array $_node) {

        $_result			            =	[];
        $_node_length                   =   0;
        $_node_start                    =   0;
        $_node_end                      =   FALSE;

        do {
            $_node_x                    =   explode('(', current($_node));

            if ($_node_x[0] == 'first'  ) {
                $_node_start            =   0;
                break;
            }

            if ($_node_x[0] == 'last'   ) {
                $_node_start            =   -1;
                break;
            }

            if ($_node_x[0] == 'eq'     ) {
                $_node_start            =   $_node_x[1];
                break;
            }

            if ($_node_x[0] == 'lt'     ) {
                $_node_start            =   0;
                $_node_end              =   $_node_x[1];
                break;
            }
            if ($_node_x[0] == 'gt'     ) {
                $_node_start            =   $_node_x[1];
                $_node_end              =   -1;
                break;
            }

            if ($_node_x[0] == 'btw'    ){
                $_node_x[1]             =   explode(',', $_node_x[1]);
                $_node_start            =   $_node_x[1][0] -1;
                $_node_end              =   $_node_x[1][1];
                break;
            }

        } while (next($_node));

        foreach ($_data_handle as $_current_data_handle) {

            $_tmp_result                =   [];

            if ($_node_end === FALSE) {
                $_tmp_result            =   $_current_data_handle->find($_find_expression, $_node_start);
            } else {
                $_tmp_result            =   $_current_data_handle->find($_find_expression);
                $_node_length           =   count($_result);
                $_tmp_result	        =	array_slice(
                                                            $_result,
                                                            $_node_start -1,
                                                            $_node_end > 0
                                                                ? $_node_end - $_node_start + 1
                                                                : $_node_length + $_node_end - $_node_start
                                                        );
            }

            $_tmp_result != NULL
                ? $_result              =   array_merge($_result, $_tmp_result) : FALSE;
        }

        return $_result;
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
            $_raw_cookie                            =   str_split(Cookie::deserialize($__RESULT['DATA']));

            foreach($_raw_cookie as $_key => $_cookie_node) {
                $__RESULT['HEADER'][]               =   (new \http\Cookie())
                                                            ->setCookie('ADS-'.$_key, $_cookie_node)
                                                            ->toArray();
            }

        }

        if ($_header_set & 4) {
            !isset($__RESULT['HEADER']) ? $__RESULT['HEADER']   = [] : NULL;
            $__RESULT['HEADER']                     =   array_merge($__RESULT['HEADER'], $_tmp_cookie);
        }

        return $__RESULT;
    }

    private static function transcoding($_raw_data, $_header, $_conf) {

        $_content_type                  =   [];

        do {
            if (isset($_conf['data']['content-type'])) {
                $_content_type[0]       =   $_conf['data']['content-type'];
            } else break;

            if (isset($_conf['data']['raw-content-type'])) {
                $_content_type[1]       =   $_conf['data']['raw-content-type'];
                break;
            }

            if (isset($_header['Content-Type'])) {
                $_tmp_set               =   explode('charset=', $_header['Content-Type']);
                if (isset($_tmp_set[1]))
                    $_content_type[1]   =   $_tmp_set[1];
                else unset($_content_type[0]);

                break;
            }

        } while(FALSE);

        return count($_content_type) == 2 ? iconv($_content_type[1], $_content_type[0], $_raw_data) : $_raw_data;
    }
}