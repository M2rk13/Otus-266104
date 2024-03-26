<?php

declare(strict_types=1);

namespace App\ProjectCascade\GateWay\HandlerRegistry;

use GuzzleHttp\Psr7\Request;

interface GateWayHandlerInterface
{
    public function handle(Request $request, ?string $playerId = null): array;
    public function authRequired(): bool;
    public static function getMethod(): string;
    public static function getUri(): string;
}
