<?php

declare(strict_types=1);

namespace App\SpaceObjects;

interface SpaceObjectInterface
{
    public function getProperty(string $key);
    public function setProperty(string $key, mixed $newValue): void;
    public function isPropertySet(string $key): bool;
}
