<?php

declare(strict_types=1);

namespace App\ProjectCascade\UseCase\AuthHandler;

use GuzzleHttp\Psr7\Request;

interface AuthInterface
{
    public function auth(Request $request): string;
}
