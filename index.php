<?php

/*
* Request forwarder, look at common/index.php for the action and license
*/

@error_reporting(E_ALL ^ E_NOTICE);
if (PHP_OS === 'Windows')
{
    @ini_set('include_path', ini_get('include_path').';.\\common\\includes');
}
else
{
    @ini_set('include_path', ini_get('include_path').':./common/includes');
}

if(!ini_get('zlib.output_compression')) ob_start("ob_gzhandler");

include('common/index.php');
?>