<?php
namespace Terrazza\Http\Routing\OpenApi\RequestHandlerBuilder;
use ReflectionClass;
use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Response\HttpResponseInterface;
use Terrazza\Http\Routing\Exception\HttpRoutingControllerException;
use Terrazza\Http\Routing\HttpRequestHandlerBuilderInterface;
use Terrazza\Http\Routing\HttpRoute;
use Terrazza\Injector\InjectorInterface;
use Throwable;

class OperationClassMethodRequestHandler implements HttpRequestHandlerBuilderInterface {
    private InjectorInterface $injector;
    private string $controllerPath;
    CONST CONTROLLER_PATH_DELIMITER                 = "/";
    CONST OPERATION_DELIMITER                       = "_";
    public function __construct(InjectorInterface $injector, string $controllerPath) {
        $this->injector                             = $injector;
        $this->controllerPath                       = $controllerPath;
    }

    /**
     * @param string $className
     * @param string $controllerPath
     * @return object
     * @throws HttpRoutingControllerException
     */
    private function getClass(string $className, string $controllerPath) : object {
        $className                                  = str_replace("{ClassName}", ucfirst($className), $controllerPath);
        $className                                  = str_replace(self::CONTROLLER_PATH_DELIMITER, "\\", $className);
        if (class_exists($className)) {
            try {
                return $this->injector->get($className);
            } catch (Throwable $exception) {
                throw new HttpRoutingControllerException("controller/class $className could not be initialized", $exception);
            }
        } else {
            throw new HttpRoutingControllerException("controller/class $className does not exist");
        }
    }

    function getRequestHandler(HttpRoute $route) : HttpRequestHandlerInterface {
        list($className, $methodName)               = explode(self::OPERATION_DELIMITER, $route->getRequestHandlerClass());
        $requestHandlerClass                        = $this->getClass($className, $this->controllerPath);
        return new class ($requestHandlerClass, $methodName) implements HttpRequestHandlerInterface {
            private object $handleClass;
            private string $handleMethod;
            public function __construct(object $handleClass, string $handleMethod) {
                $this->handleClass                  = $handleClass;
                $this->handleMethod                 = $handleMethod;
            }

            /**
             * @param HttpRequestInterface $request
             * @return HttpResponseInterface
             */
            public function handle(HttpRequestInterface $request): HttpResponseInterface {
                if ($this->hasMethod($this->handleClass, $this->handleMethod)) {
                    return call_user_func([$this->handleClass, $this->handleMethod], $request);
                } else {
                    throw new HttpRoutingControllerException("method ".$this->handleMethod." for controller/class ".get_class($this->handleClass)." does not exist");
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