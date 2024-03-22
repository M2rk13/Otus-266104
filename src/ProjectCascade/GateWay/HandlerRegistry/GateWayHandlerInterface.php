<?php

namespace App\ProjectCascade\GateWay\HandlerRegistry;

use GuzzleHttp\Psr7\Request;

interface GateWayHandlerInterface
{
    public function handle(Request $request, ?string $playerId = null): array;
    public function authRequired(): bool;
}
