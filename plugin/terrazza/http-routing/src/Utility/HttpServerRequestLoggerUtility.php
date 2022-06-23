<?php
namespace Terrazza\Http\Routing\Utility;

use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Request\HttpServerRequestHandlerInterface;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Response\HttpResponseInterface;
use Terrazza\Logger\LoggerInterface;
use Throwable;

class HttpServerRequestLoggerUtility implements HttpServerRequestHandlerInterface {
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger) {
        $this->logger                               = $logger;
    }

    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        $start                                      = microtime(true);
        try {
            $response                               = $requestHandler->handle($request);
        } catch (Throwable $exception) {
            $response                               = new HttpResponse(500);
        }
        $time_elapsed_secs                          = microtime(true) - $start;
        $this->logger->info($request->getMethod()." ".$request->getRequestTarget().", STATUS=".$response->getStatusCode().", RUNTIME=".number_format($time_elapsed_secs,3)."s, TYPE=".$response->getHeaderLine("Content-Type"));
        return $response;
    }
}