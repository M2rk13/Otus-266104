<?php

declare(strict_types=1);

namespace App\ProjectCascade\Command;

use App\Command\CommandInterface;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeDefinition;
use App\ProjectCascade\Service\IoCResolverService;
use Exception;

readonly class RabbitDefinitionUpdateCommand implements CommandInterface
{
    public function __construct(private array $definitionList) {}

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
