<?php
namespace Terrazza\Kernel;

use Terrazza\Http\Message\HttpMessageAdapter;
use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpServerRequestBuilder;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Routing\Exception\HttpMethodNotAllowedException;
use Terrazza\Http\Routing\Exception\HttpRouteNotFoundException;
use Terrazza\Http\Routing\Exception\HttpUnsupportedContentType;
use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Kernel\Helper\LoggerHelper;
use Terrazza\Injector\Injector;
use Throwable;

class HttpKernel {
    private string $env;
    private bool $debug;
    public function __construct(string $env, bool $debug) {
        $this->env                                  = $env;
        $this->debug                                = $debug;
    }

    public function handle() : void {
        $logger                                     = (new LoggerHelper("framework"))->createLogger("../logger.log");
        try {
            $injector                               = new Injector(require_once("../config/di.config.php"), $logger);

            // get request from server
            $request                                = (new HttpServerRequestBuilder)->getServerRequest();

            // find route
            /** @var HttpRouterInterface $router */
            $router                                 = $injector->get(HttpRouterInterface::class);

            /** @var HttpRequestHandlerInterface $requestHandler */
            $requestHandler                         = $router->getRequestHandler($request);
            $response                               = $requestHandler->handle($request);
        } catch (HttpRouteNotFoundException|HttpMethodNotAllowedException|HttpUnsupportedContentType $exception) {
            $logger->notice($exception->getMessage());
            $response                               = new HttpResponse($exception->getCode());
        } catch (Throwable $exception) {
            $logger->critical($exception->getMessage(), ["exception" => $exception]);
            $response                               = new HttpResponse(500);
        }
        (new HttpMessageAdapter())->emitResponse($response);
    }
}