<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/1/26
 * Time: 下午3:29
 */

$_result = [];
$_result['http-method'] = $_SERVER['REQUEST_METHOD'];

echo json_encode($_result, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);