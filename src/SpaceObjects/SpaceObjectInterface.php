<?php

declare(strict_types=1);

namespace App\SpaceObjects;

interface SpaceObjectInterface
{
    public function getProperty(string $key);
    public function setProperty(string $key, $newValue): void;
}
