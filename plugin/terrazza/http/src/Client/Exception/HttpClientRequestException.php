<?php
declare(strict_types=1);
namespace Terrazza\Http\Client\Exception;
use Psr\Http\Client\RequestExceptionInterface;

class HttpClientRequestException extends HttpClientException implements RequestExceptionInterface {}