<?php

require("vendor/autoload.php");
$openapi = \OpenApi\scan('/Modules/Newsletter');
header('Content-Type: application/x-json');
echo $openapi->toJSON();