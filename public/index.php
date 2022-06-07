<?php
require "../plugin/autoload.php";
use Terrazza\Kernel\HttpKernel;
putenv("APP_ENV=dev");
putenv("APP_DEBUG=false");
(new HttpKernel(getenv("APP_ENV"), getenv("APP_DEBUG")))->handle();