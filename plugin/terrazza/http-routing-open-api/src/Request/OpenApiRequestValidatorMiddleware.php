<?php
namespace Terrazza\Http\Routing\OpenApi\Request;

use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Request\HttpRequestMiddlewareInterface;
use Terrazza\Http\Response\HttpResponseInterface;
use Terrazza\Http\Routing\HttpRequestValidatorInterface;

class OpenApiRequestValidatorMiddleware implements HttpRequestMiddlewareInterface {
    private HttpRequestValidatorInterface $validator;
    public function __construct(HttpRequestValidatorInterface $validator) {
        $this->validator                            = $validator;
    }

    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        $route                                      = $this->validator->getRouting()->getRoute($request->getUri(), $request->getMethod());
        if ($route) {
            $this->validator->validateParams($route, $request);
            var_dump(__METHOD__.":before");
        }
        $result 								    = $requestHandler->handle($request);
        if ($route) {
            var_dump(__METHOD__.":after");
        }
        return $result;
    }
}