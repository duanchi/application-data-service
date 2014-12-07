<?php
/**
 * File    libraries\VOP\Process\Request.php
 * Desc    请求预处理模块
 * Manual  svn://svn.vop.com/api/manual/Plugin/Request
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-23
 * Time    16:15
 */

namespace Process;


class RequestModel {

    /**
     * Function pretreatment
     * @param $_get
     * @param $_post
     * @param $_version
     * @param $_scope
     * @param $_interface
     * @return array
     */
    public static function get($_http_method, $_request_uri) {

        $__RESULT = [
            'method'        => HTTP_GET,
            'uri'           => NULL,
            'uri-scheme'    => URI_SCHEME_HTTP,
            'content-type'  => TYPE_JSON,
            'version'       => REQUEST_VERSION_NULL,
            'ranges'        => [
                'columns'       => NULL,
                'order'         => NULL,
                'limit'         => NULL,
            ],

            'access-token'  => NULL,
            'client-token'  => NULL,
            'client-id'     => NULL,
            'client-ip'     => NULL,

            'ads-parameters'=> [],
        ];

        //MAKE URL PRE-REQUEST

        //URI & SCHEME
        $_tmp_request_uri = explode('|ads?', substr($_request_uri, 1, -1));

        $_tmp_uri_scheme = strtoupper(parse_url($_tmp_request_uri[0], PHP_URL_SCHEME));
        $_tmp_uri_scheme = (defined('URI_SCHEME_' . $_tmp_uri_scheme) ? constant('URI_SCHEME_' . $_tmp_uri_scheme) : NULL);

        !empty($_tmp_request_uri[1]) ? parse_str($_tmp_request_uri[1], $_tmp_ads_parameters) : $_tmp_ads_parameters = [];

        //CONTENT-TYPE
        $_tmp_content_type = strtoupper(explode(ADS_DOMAIN, $_SERVER['HTTP_HOST']));
        if (!empty($_tmp_host[0])) {
            $_tmp_content_type = (defined('TYPE_' . $_tmp_host[0]) ? constant('TYPE_' . $_tmp_host[0]) : NULL);
        }


        //MAKE URL REQUEST RESULT
        $__RESULT['method']         = constant('HTTP_'.$_http_method);
        $__RESULT['uri']            = $_tmp_request_uri[0];
        $__RESULT['ads-parameters'] = $_tmp_ads_parameters;

        !empty($_tmp_uri_scheme)                ? $__RESULT['uri-scheme']   = $_tmp_uri_scheme              : NULL ;
        !empty($_tmp_content_type)              ? $__RESULT['content-type'] = $_tmp_content_type            : NULL ;
        isset($_SERVER['HTTP_ACCESS_TOKEN'])    ? $__RESULT['access-token'] = $_SERVER['HTTP_ACCESS_TOKEN'] : NULL;
        isset($_SERVER['HTTP_CLIENT_TOKEN'])    ? $__RESULT['client-token'] = $_SERVER['HTTP_CLIENT_TOKEN'] : NULL;
        isset($_SERVER['HTTP_CLIENT_ID'])       ? $__RESULT['client-id']    = $_SERVER['HTTP_CLIENT_ID']    : NULL;
        isset($_SERVER['REMOTE_ADDR'])          ? $__RESULT['client-ip']    = $_SERVER['REMOTE_ADDR']       : NULL;


        //var_dump($__RESULT['uri'], $__RESULT['ads-parameters']);


        //MAKE HTTP HEADER REQUEST
        $_tmp_header_request            = [];

        //@todo Ranges
        //@todo Domain Response Types
        //@todo Headers


        return $__RESULT;
    }
}