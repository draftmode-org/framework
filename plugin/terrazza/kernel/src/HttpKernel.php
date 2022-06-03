<?php
namespace Terrazza\Kernel;

use Terrazza\Http\Request\HttpServerRequestBuilder;
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

    public function handle() {
        $logger                                     = (new LoggerHelper("framework"))->createLogger("log.txt");
        try {
            $injector                               = new Injector(require_once("../config/di.config.php"), $logger);

            // get request from server
            $request                                = (new HttpServerRequestBuilder)->getServerRequest();

            /** @var HttpRouterInterface $router */
            $router                                     = $injector->get(HttpRouterInterface::class);
            if ($requestHandler = $router->getRequestHandler($request)) {
                $requestHandler->handle($request);
            }
        } catch (Throwable $exception) {
            $logger->critical($exception->getMessage(), ["exception" => $exception]);
        }
    }
}