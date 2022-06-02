<?php
require "../plugin/autoload.php";

use Terrazza\Framework\Helper\LoggerHelper;
use Terrazza\Http\Request\HttpRequest;

use Terrazza\Injector\Injector;
use Terrazza\Routing\RouteMatcher;

use Terrazza\Http\Routing\OpenApi\OpenApiRouting;
use Terrazza\Http\Routing\OpenApi\OpenApiYaml;
use Terrazza\Http\Routing\OpenApi\OpenApiRouter;

$logger                     = (new LoggerHelper("framework"))->createLogger("log.txt");
$injector                   = new Injector(require_once("../config/di.config.php"), $logger);

$uri                        = strtr($_SERVER["REQUEST_URI"], ["/terrazza/framework" => ""]);
$request 				    = new HttpRequest($uri, "get");

$routeMatcher 				= new RouteMatcher();
$openApiYaml				= new OpenApiYaml("../api.yaml");
$routing 					= new OpenApiRouting( $routeMatcher, $openApiYaml);
$router                     = new OpenApiRouter($routing, $injector, $logger);

if ($requestHandler = $router->getRequestHandler($request)) {
    //$validator              = $injector->get(OpenApiRequestValidator::class);
    var_dump("routing for ".$request->getUri()." and method ".$request->getMethod()." found");
} else {
    var_dump("routing for ".$request->getUri()." and method ".$request->getMethod()." not found");
}