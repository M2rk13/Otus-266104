<?php

declare(strict_types=1);

namespace App\Maintenance\Interpreter;

class InterpretObject
{

    private array $parameterList;

    public function __construct(
        private readonly string $authToken,
        private readonly string $objectId,
        private readonly string $action,
        /** @var InterpretObjectParameter[] $parameterList */
        ?array $parameterList = [],
    ) {
        if (empty($parameterList)) {
            $this->parameterList = [];

            return;
        }

        foreach ($parameterList as $parameter) {
            $type = $parameter['type'];
            $value = $parameter['value'];

            $this->parameterList[] = new InterpretObjectParameter($type, $value);
        }
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getParameterList(): array
    {
        return $this->parameterList;
    }
}
