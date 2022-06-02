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

// fake request
$request_uri                = strtr($_SERVER["REQUEST_URI"], ["/terrazza/framework" => ""]);
$uri                        = new \Terrazza\Http\Message\Uri\Uri($request_uri);
$request 				    = new HttpRequest($uri, "post", ["Content-Type" => "application/json"]);

// route
$routeMatcher 				= new RouteMatcher();
$openApiYaml				= new OpenApiYaml("../api.yaml");
$routing 					= new OpenApiRouting( $routeMatcher, $openApiYaml);
$router                     = new OpenApiRouter($routing, $injector, $logger);

if ($requestHandler = $router->getRequestHandler($request)) {
    //$validator              = $injector->get(OpenApiRequestValidator::class);
    echo "<p style='color:green'>routing for ".$request->getUri()." and method ".$request->getMethod()." found</p>";
} else {
    echo "<p style='color:red'>routing for ".$request->getUri()." and method ".$request->getMethod()." not found</p>";
}