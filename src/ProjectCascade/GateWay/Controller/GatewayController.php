<?php

declare(strict_types=1);

namespace App\ProjectCascade\GateWay\Controller;

use App\ProjectCascade\Exception\PlayerNotFoundException;
use App\ProjectCascade\Exception\UriHandlerNotFoundException;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerRegistry;
use App\ProjectCascade\Service\IoCResolverService;
use App\ProjectCascade\UseCase\AuthHandler\AuthInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Throwable;

class GatewayController
{
    private AuthInterface $authHandler;
    private GateWayHandlerRegistry $handlerRegistry;

    public function __construct()
    {
        /** @var AuthInterface $authHandler */
        $authHandler = IoCResolverService::getClass(AuthInterface::class);
        $this->authHandler = $authHandler;

        /** @var GateWayHandlerRegistry $handlerRegistry */
        $handlerRegistry = IoCResolverService::getClass(GateWayHandlerRegistry::class);
        $this->handlerRegistry = $handlerRegistry;
    }

    public function gateway(Request $request): Response
    {
        try {
            $route = $request->getUri();
            $method = $request->getMethod();

            $handler = $this->handlerRegistry->getHandler($route->getPath(), $method);
            $playerId = null;

            if ($handler->authRequired()) {
                $playerId = $this->authHandler->auth($request);
            }

            $responseData = $handler->handle($request, $playerId);

            $status = 200;
            $action = $responseData['action'] ?: 'ok';

            if ($action === 'created') {
                $status = 201;
            }

            $source = $responseData['source'] ?? [];
            $headers = ['source' => $source];
            $headers = array_filter($headers);

            unset($responseData['action'], $responseData['source']);

            $response = [
                'status' => $status,
                'headers' => $headers,
                'body' => json_encode($responseData, JSON_THROW_ON_ERROR),
            ];
            $response = array_filter($response);

            return new Response(...$response);
        } catch (PlayerNotFoundException) {
            return new Response(401);
        } catch (UriHandlerNotFoundException) {
            return new Response(404);
        } catch (Throwable $e) {
            // log exception

            return new Response(500);
        }
    }
}
