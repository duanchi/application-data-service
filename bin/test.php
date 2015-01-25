<?php
//var_dump(filter_var('/^http://localhost/login/login.action(.*)/', FILTER_VALIDATE_REGEXP));

var_dump(filter_var('file://api.ads.devel/env.php', FILTER_VALIDATE_URL));

define('$_xx', 1);

var_dump(get_defined_constants(TRUE));

$_find = ['$x','$1'];

$_replace = ['haha', 'object'];

$_string = 'title #{$x} #{$1}';

var_dump(str_replace($_find, $_replace, $_string));