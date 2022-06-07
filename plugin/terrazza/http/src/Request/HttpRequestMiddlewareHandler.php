<?php
namespace Terrazza\Http\Request;
use Generator;
use Terrazza\Http\Response\HttpResponseInterface;

class HttpRequestMiddlewareHandler implements HttpRequestMiddlewareHandlerInterface {
    /** @var HttpRequestMiddlewareHandlerInterface[]  */
    private array $middlewares;
    public function __construct(...$middlewares) {
        $this->middlewares = $middlewares;
    }

    /**
     * @return Generator
     */
    private function getGenerator() : Generator {
        foreach ($this->middlewares as $middleware) {
            yield $middleware;
        }
    }

    /**
     * @param HttpRequestInterface $request
     * @param HttpRequestHandlerInterface $requestHandler
     * @return HttpResponseInterface
     */
    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        return (new class ($this->getGenerator(), $requestHandler) implements HttpRequestHandlerInterface {
            private Generator $generator;
            private HttpRequestHandlerInterface $requestHandler;

            public function __construct(Generator $generator, HttpRequestHandlerInterface $requestHandler) {
                $this->generator                    = $generator;
                $this->requestHandler               = $requestHandler;
            }

            public function handle(HttpRequestInterface $request) : HttpResponseInterface {
                if (!$this->generator->valid()) {
                    return $this->requestHandler->handle($request);
                }
                /** @var HttpRequestMiddlewareHandlerInterface $current */
                $current                            = $this->generator->current();
                $this->generator->next();
                return $current->handle($request, $this);
            }
        })->handle($request);
    }
}