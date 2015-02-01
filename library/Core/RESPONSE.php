<?php
/**
 * Created by PhpStorm.
 * User: 翅
 * Date: 2014/10/23
 * Time: 15:08
 */

namespace CORE;


class RESPONSE {

    private static $__response_type = RESPONSE_TYPE_YAF;
    private static $__instance      = NULL;

    public static function initialize($_instance = NULL, $_response_type = RESPONSE_TYPE_NATIVE) {
        switch($_response_type) {
            case RESPONSE_TYPE_NATIVE :
                self::$__response_type = RESPONSE_TYPE_NATIVE;
                break;

            case RESPONSE_TYPE_YAF :
            default:
                self::$__response_type = RESPONSE_TYPE_YAF;
                self::$__instance = $_instance;
                break;
        }
        return TRUE;
    }

    public static function set($_content = NULL, $_scope = RESPONSE_BODY) {

        switch($_scope) {
            case RESPONSE_HEADER:
                if (is_array($_content)) {
                    foreach($_content as $key => $value) {
                        self::$__instance->setHeader($key, $value);
                    }
                }
                self::respond();
                break;

            case RESPONSE_BODY:
            default:
            if (is_array($_content)) {
                self::$__instance->setBody($_content[0], $_content[1]);
            } else {
                self::$__instance->setBody($_content);
            }

                break;
        }
    }

    public static function respond() {
        self::$__instance->response();
    }
} 