<?php
namespace Terrazza\Kernel;

use Terrazza\Http\Message\HttpMessageAdapter;
use Terrazza\Http\Request\HttpServerRequestBuilder;
use Terrazza\Http\Request\HttpServerRequestHandlerInterface;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Routing\Exception\HttpRouteNotFoundException;
use Terrazza\Http\Routing\Exception\HttpRoutingControllerException;
use Terrazza\Http\Routing\HttpRequestHandlerBuilderInterface;
use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Kernel\Helper\LoggerHelper;
use Terrazza\Injector\Injector;
use Terrazza\Logger\Logger;
use Throwable;

class HttpKernel {
    private string $env;
    private bool $debug;
    public function __construct(string $env, bool $debug) {
        $this->env                                  = $env;
        $this->debug                                = $debug;
    }

    public function handle(string $applicationName, string $diConfigPath) : void {
        $logger                                     = (new LoggerHelper($applicationName))
            ->createLogger(Logger::INFO, "../logger.log");
        try {
            $diConfigFile                           = $diConfigPath . DIRECTORY_SEPARATOR . "di.config.php";
            if (!file_exists($diConfigFile)) {
                throw new \RuntimeException("$diConfigFile does not exists");
            }
            $injector                               = new Injector(require_once($diConfigFile), $logger);

            // get request from server
            $request                                = (new HttpServerRequestBuilder)->getServerRequest();

            // get route (not found throws an exception)
            /** @var HttpRouterInterface $router */
            $router                                 = $injector->get(HttpRouterInterface::class);
            $httpRoute                              = $router->getRoute($request);

            // get requestHandler (not found or any initialize problems throws an exception)
            /** @var HttpRequestHandlerBuilderInterface $requestHandlerBuilder */
            $requestHandlerBuilder                  = $injector->get(HttpRequestHandlerBuilderInterface::class);
            $requestHandler                         = $requestHandlerBuilder->getRequestHandler($httpRoute);

            // load serverRequestHandler
            /** @var HttpServerRequestHandlerInterface $serverRequestHandler */
            $serverRequestHandler                   = $injector->get(HttpServerRequestHandlerInterface::class);

            $response                               = $serverRequestHandler->handle($request, $requestHandler);

        } catch (HttpRouteNotFoundException $exception) {
            $logger->notice($exception->getMessage());
            $response                               = new HttpResponse($exception->getCode());
        } catch (HttpRoutingControllerException $exception) {
            $logger->critical($exception->getMessage(), ["exception" => $exception]);
            $response                               = new HttpResponse($exception->getCode());
        } catch (Throwable $exception) {
            $logger->critical($exception->getMessage(), ["exception" => $exception]);
            $response                               = new HttpResponse(500);
        }
        (new HttpMessageAdapter())->emitResponse($response);
    }
}