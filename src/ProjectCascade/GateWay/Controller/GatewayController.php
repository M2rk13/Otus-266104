<?php

namespace App\ProjectCascade\GateWay\Controller;

use App\ProjectCascade\Exception\PlayerNotFoundException;
use App\ProjectCascade\Exception\UriHandlerNotFoundException;
use App\ProjectCascade\GateWay\HandlerRegistry\GateWayHandlerRegistry;
use App\ProjectCascade\UseCase\AuthHandler\AuthHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Throwable;

class GatewayController
{
    private AuthHandler $authHandler;
    private GateWayHandlerRegistry $handlerRegistry;

    public function __construct()
    {
        $this->authHandler = new AuthHandler();
        $this->handlerRegistry = new GateWayHandlerRegistry();
    }

    public function gateway(Request $request): Response
    {
        try {
            $route = $request->getUri();
            $method = $request->getMethod();

            $handler = $this->handlerRegistry->getHandler($route, $method);
            $playerId = null;

            if ($handler->authRequired()) {
                $playerId = $this->authHandler->getPlayerId($request);
            }

            $responseData = $handler->handle($request, $playerId);

            $status = 200;
            $action = $responseData['action'] ?: 'ok';

            if ($action === 'created') {
                $status = 201;
            }

            $source = $responseData['source'] ?: [];
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
