<?php

declare(strict_types=1);

namespace App\ProjectCascade\GateWay\HandlerRegistry;

use App\ProjectCascade\Exception\UriHandlerNotFoundException;
use App\ProjectCascade\GateWay\UseCase\CallbackHandler\CallbackHandler;
use App\ProjectCascade\GateWay\UseCase\PaymentHandler\GateWayPaymentHandler;

class GateWayHandlerRegistry
{
    // это реестр, который можно организовать разными способами автозагрузки,
    // условно обозначим хардкодом
    /** @var GateWayHandlerInterface[] */
    private array $actionRegistry = [
        'payment' => [
            'POST' => GateWayPaymentHandler::class,
        ],
        'callback' => [
            'POST' => CallbackHandler::class
        ]
        // ...
        // ...
        // ...
    ];

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
