<?php
namespace Terrazza\Http\Routing\OpenApi\RequestHandlerBuilder;
use ReflectionClass;
use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Response\HttpResponseInterface;
use Terrazza\Http\Routing\HttpRequestHandlerBuilderInterface;
use Terrazza\Http\Routing\HttpRoute;
use Terrazza\Injector\InjectorInterface;

class OperationClassMethodRequestHandler implements HttpRequestHandlerBuilderInterface {
    private InjectorInterface $injector;
    private string $controllerPath;
    public function __construct(InjectorInterface $injector, string $controllerPath) {
        $this->injector                             = $injector;
        $this->controllerPath                       = $controllerPath;
    }

    function getRequestHandler(HttpRoute $route) : HttpRequestHandlerInterface {
        return new class ($route, $this->injector, $this->controllerPath) implements HttpRequestHandlerInterface {
            private HttpRoute $route;
            private InjectorInterface $injector;
            private string $controllerPath;
            CONST CONTROLLER_PATH_DELIMITER         = "/";
            CONST OPERATION_DELIMITER               = "_";

            public function __construct(HttpRoute $route, InjectorInterface $injector, string $controllerPath) {
                $this->route                        = $route;
                $this->injector                     = $injector;
                $this->controllerPath               = $controllerPath;
            }

            /**
             * @param HttpRequestInterface $request
             * @return HttpResponseInterface
             */
            public function handle(HttpRequestInterface $request): HttpResponseInterface {
                list($className, $methodName)       = explode(self::OPERATION_DELIMITER, $this->route->getRequestHandlerClass());
                $requestHandlerClass                = $this->injectClass($className, $this->controllerPath);
                if ($this->hasMethod($requestHandlerClass, $methodName)) {
                    return call_user_func([$requestHandlerClass, $methodName], $request);
                } else {
                    return new HttpResponse();
                }
            }

            /**
             * @param string $className
             * @param string $controllerPath
             * @return object
             */
            private function injectClass(string $className, string $controllerPath) : object {
                $className                          = str_replace("{ClassName}", ucfirst($className), $controllerPath);
                $className                          = str_replace(self::CONTROLLER_PATH_DELIMITER, "\\", $className);
                if (class_exists($className)) {
                    return $this->injector->get($className);
                } else {
                    throw new \RuntimeException($className. "not found");
                }
            }

            /**
             * @param object $class
             * @param string $methodName
             * @return string
             */
            private function hasMethod(object $class, string $methodName) : string {
                $refClass                       = new ReflectionClass($class);
                return $refClass->hasMethod($methodName);
            }
        };
    }
}