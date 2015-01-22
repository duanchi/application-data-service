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

    public  static function parse_data($_raw_data, $_conf) {
        $__RESULT               =   FALSE;
        $_data_type             =   isset($_conf['data']['type'])
                                    ?
                                    $_conf['data']['type'] : 'stream';

        switch($_data_type) {

        }
    }
}