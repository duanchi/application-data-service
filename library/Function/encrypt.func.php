<?php
/**
 * Created by PhpStorm.
 * User: lovemybud
 * Date: 15/2/1
 * Time: 19:00
 */

const ENCRYPT_MD5       =   0;
const ENCRYPT_SHA1      =   1;
const ENCRYPT_SHA256    =   2;
const ENCRYPT_HASH      =   3;
const ENCRYPT_BASE64    =   4;

function encrypt($_method = ENCRYPT_MD5, $_string, $_key = NULL) {

    $_result            =   FALSE;

    switch($_method) {

        case ENCRYPT_BASE64:
            $_result    =   base64_encode($_string);
            break;

        case ENCRYPT_MD5:
        default:
            $_result    =   md5($_string);
            break;
    }

    return $_result;
}

function decrypt($_method = ENCRYPT_BASE64, $_string, $_key = NULL) {

    $_result            =   FALSE;

    switch($_method) {

        case ENCRYPT_BASE64:
        default:
            $_result    =   base64_decode($_string);
            break;
    }

    return $_result;
}