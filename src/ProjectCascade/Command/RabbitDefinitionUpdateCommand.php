<?php

namespace App\ProjectCascade\Command;

use App\Command\CommandInterface;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeDefinition;
use App\ProjectCascade\Service\IoCResolverService;
use Exception;

class RabbitDefinitionUpdateCommand implements CommandInterface
{
    public function __construct(private readonly array $definitionList) {}

    # не буду тут углубляться в реализацию, сделаю хардкод,
    # так как обертка для работы с rabbit вполне могла бы тянуть на отдельную тему курсовой
    # поэтому реализация будет на минимальном уровне, лишь бы работало
    /**
     * @throws Exception
     */
    public function execute(): void
    {
        /** @var CascadeDefinition $definition */
        foreach ($this->definitionList as $definition) {
            $definition->init(IoCResolverService::getRabbitConnection());
        }
    }
}
