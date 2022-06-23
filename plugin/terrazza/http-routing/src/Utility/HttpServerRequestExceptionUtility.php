<?php
namespace Terrazza\Http\Routing\Utility;

use InvalidArgumentException;
use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Request\HttpServerRequestHandlerInterface;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Response\HttpResponseInterface;
use Throwable;

class HttpServerRequestExceptionUtility implements HttpServerRequestHandlerInterface {
    private array $contentTypeMapper                = ["*/*" => "application/json"];
    private array $exceptionInterfaceCodes;
    public function __construct(array $exceptionInterfaceCodes=null) {
        $this->exceptionInterfaceCodes              = $exceptionInterfaceCodes ?? [];
    }

    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        try {
            $response                               = $requestHandler->handle($request);
        } catch (Throwable $exception) {
            $response                               = $this->handleException($request, $exception);
        }
        return $response;
    }

    private function handleException(HttpRequestInterface $request, Throwable $exception) : HttpResponseInterface {
        $header                                     = [];

        // use header from request, default: application/json
        if ($request->hasHeader("Accept")) {
            $accept                                 = $request->getHeaderLine("Accept");
        } else {
            $accept                                 = "application/json";
        }
        $accept                                     = $this->contentTypeMapper[$accept] ?? $accept;

        // common response
        $content = [
            'request'                               => [
                "method"                            => $request->getMethod(),
                "uri"                               => $request->getUri()->getPath(),
                "params"                            => $request->getUri()->getQuery(),
                "payload"                           => $request->getBody()->getSize()
            ],
            "response" => [
                'code'                              => $exception->getCode(),
                'reason'                            => $exception->getMessage(),
                'trigger'                           => basename(get_class($exception))
            ]
        ];

        // format, based on content-type
        if (preg_match("#text/html#", $accept)) {
            $header["Content-Type"]                 = "text/html";
            $content                                = $this->format_html($content);
        } elseif (preg_match("#text/plain#", $accept)) {
            $header["Content-Type"]                 = "text/plain";
            $content                                =  $exception->getMessage();
        } elseif (preg_match("#application/json#", $accept)) {
            $header["Content-Type"]                 = $accept;
            $content                                = $this->format_json($content);
        }

        // set responseCode, based on exceptionInterfaceMapping
        $statusCode                                 = null;
        foreach ($this->exceptionInterfaceCodes as $instanceOf => $exceptionCode) {
            if ($exception instanceof $instanceOf) {
                $statusCode                       = $exceptionCode;
                break;
            }
        }

        // set responseCode, based on exceptionInterface
        if (!$statusCode) {
            switch (true) {
                case $exception instanceof InvalidArgumentException:
                    $statusCode                     = 400;
                    break;
                default:
                    $statusCode                     = 500;
            }
        }

        return new HttpResponse($statusCode, $header, $content);
    }

    private function format_json(array $content) : string {
        return json_encode($content);
    }

    private function format_html(array $content) : string {
        $body                                       = $this->format_html_row("request", $content["request"]);
        $body                                      .= $this->format_html_row("response", $content["response"]);
        return $body;
    }
    private function format_html_row(string $node, array $content) : string {
        $body                                       = "<{$node}>";
        foreach ($content as $k => $v) {
            $body                                   .= "<{$k}>{$v}</{$k}>";
        }
        return $body."</{$node}>";
    }
}