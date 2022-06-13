<?php

namespace Terrazza\Http\Routing\Utility;

use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Request\HttpServerRequestHandlerInterface;
use Terrazza\Http\Response\HttpResponseInterface;
use Terrazza\Logger\LoggerInterface;

class HttpServerRequestLoggerUtility implements HttpServerRequestHandlerInterface {
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger) {
        $this->logger                               = $logger;
    }

    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        $message                                    = "[".$request->getMethod()."] ".$request->getRequestTarget();
        $this->logger->info($message);
        $response                                   = $requestHandler->handle($request);
        $this->logger->info($message.", ".$response->getStatusCode());
        return $response;
    }
}