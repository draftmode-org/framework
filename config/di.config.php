<?php

use Terrazza\Http\Request\HttpServerRequestHandlerInterface;
use Terrazza\Http\Routing\Factory\HttpRouterFactory;
use Terrazza\Http\Routing\HttpRequestHandlerBuilderInterface;
use Terrazza\Http\Routing\HttpRouteLoaderInterface;
use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Http\Routing\OpenApi\OpenApiYaml;
use Terrazza\Http\Routing\OpenApi\RequestHandlerBuilder\OperationClassMethodRequestHandler;
use Terrazza\Injector\Injector;
use Terrazza\Injector\InjectorInterface;
return [
    /** core */
    InjectorInterface::class                        => Injector::class,

    /** routing - router (native)
        HttpRouterInterface::class                      => HttpRouter::class,
        RouteMatcherInterface::class                    => RouteMatcher::class,
        HttpRoutingInterface::class                     => HttpRouting::class,
    */
    /** routing - router (factory) */
    HttpRouterInterface::class                      => HttpRouterFactory::class,

    HttpRouteLoaderInterface::class                 => OpenApiYaml::class,
    OpenApiYaml::class                              => ["yamlFilename" => getenv("APP_CONFIG_FOLDER")."/api.yaml"],

    /** routing - requestHandlerBuilder */
    HttpRequestHandlerBuilderInterface::class       => OperationClassMethodRequestHandler::class,
    OperationClassMethodRequestHandler::class       => ["controllerPath" => "Terrazza/Framework/Controller/Http/Http{ClassName}Controller"],

    /** request handler */
    //HttpServerRequestHandlerInterface::class        => \Terrazza\Http\Request\HttpServerRequestHandler::class
    HttpServerRequestHandlerInterface::class        => \Terrazza\Http\Request\HttpServerRequestMiddlewareHandler::class,
    \Terrazza\Http\Request\HttpServerRequestMiddlewareHandler::class => [
        \Terrazza\Http\Routing\Utility\HttpServerRequestLoggerUtility::class,
        \Terrazza\Http\Routing\Utility\HttpServerRequestExceptionUtility::class
    ]
];