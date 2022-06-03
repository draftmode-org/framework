<?php
declare(strict_types=1);
namespace Terrazza\Http\Request;
use Terrazza\Http\Message\HttpMessageAdapter;
use Terrazza\Http\Response\HttpResponseInterface;

class HttpServerRequestBuilder implements HttpServerRequestBuilderInterface {
    private ?HttpServerRequestInterface $request=null;

    /**
     * @param HttpRequestHandlerInterface $requestHandler
     * @return HttpResponseInterface
     */
    public function handle(HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        if ($this->request === null) {
            $this->request                          = $this->getServerRequest();
        }
        return $requestHandler->handle($this->request);
    }

    /**
     * @return HttpServerRequestInterface
     */
    public function getServerRequest() : HttpServerRequestInterface {
        $messageAdapter                             = new HttpMessageAdapter();
        $this->request                              = $messageAdapter->getServerRequestFromGlobals();
        return $this->request;
    }
}