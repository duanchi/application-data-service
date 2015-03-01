<?php
/**
 * Created by PhpStorm.
 * User: lovemybud
 * Date: 15/3/1
 * Time: 19:08
 */

namespace IO\HTTP2;


class Cookie {

    const ENCRYPT_BASE64    =   0;
    const ENCRYPT_DES       =   1;

    static public function serialize($_cookie, $_encrypt_method = self::ENCRYPT_BASE64) {

        $_result            = NULL;

        switch ($_encrypt_method) {

            case self::ENCRYPT_BASE64:
                $_result    =   \msgpack_unpack(
                                                    decrypt(
                                                                ENCRYPT_BASE64,
                                                                $_cookie
                                                            )
                                                );
                break;
        }

        return $_result;
    }

    static public function deserialize($_cookie, $_encrypt_method = self::ENCRYPT_BASE64) {

        $_result            =   NULL;

        switch ($_encrypt_method) {

            case self::ENCRYPT_BASE64:
                $_result    =   str_split(
                                            encrypt(
                                                        ENCRYPT_BASE64,
                                                        \msgpack_pack($_cookie)
                                            ),
                                            4 * 4096
                                        );
                break;
        }

        return $_result;
    }
}