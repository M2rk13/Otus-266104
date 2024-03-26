<?php

declare(strict_types=1);

namespace App\ProjectCascade\GateWay\HandlerRegistry;

use App\ProjectCascade\Exception\UriHandlerNotFoundException;
use App\ProjectCascade\GateWay\UseCase\CallbackHandler\CallbackHandler;
use App\ProjectCascade\GateWay\UseCase\PaymentHandler\GateWayPaymentHandler;

class GateWayHandlerRegistry
{
    /** @var GateWayHandlerInterface[] */
    private array $actionRegistry;

    public function __construct()
    {
        $this->actionRegistry = $GLOBALS['gateway'];
    }

    /**
     * @throws UriHandlerNotFoundException
     */
    public function getHandler(string $uri, ?string $method): GateWayHandlerInterface
    {
        $method = $method ?: 'GET';
        $method = strtoupper($method);

        $name = $this->actionRegistry[$uri][$method] ?? null;

        if (empty($name)) {
            throw new UriHandlerNotFoundException(sprintf('%s %s', $method, $uri));
        }

        return new $name();
    }
}
