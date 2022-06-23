<?php
require "../plugin/autoload.php";
use Terrazza\Kernel\HttpKernel;
putenv("APP_ENV=dev");
putenv("APP_DEBUG=false");
putenv("APP_NAME=Framework");
putenv("APP_CONFIG_FOLDER=../config");

(new HttpKernel(getenv("APP_ENV"), getenv("APP_DEBUG")==="true"))->
    handle(getenv("APP_FRAMEWORK"),getenv("APP_CONFIG_FOLDER"));
