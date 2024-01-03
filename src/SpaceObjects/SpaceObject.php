<?php

declare(strict_types=1);

namespace App\SpaceObjects;

use App\Exception\PropertyNotFoundException;

abstract class SpaceObject implements SpaceObjectInterface
{
    private array $properties = [];

    /**
     * @throws PropertyNotFoundException
     */
    public function getProperty(string $key)
    {
        if ($this->isPropertySet($key) === false) {
            throw new PropertyNotFoundException($key);
        }

        return $this->properties[$key];
    }

    public function setProperty(string $key, $newValue): void
    {
        $this->properties[$key] = $newValue;
    }

    public function isPropertySet(string $key): bool
    {
        return isset($this->properties[$key]);
    }
}
