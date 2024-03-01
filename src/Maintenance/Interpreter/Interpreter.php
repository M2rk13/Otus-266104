<?php

declare(strict_types=1);

namespace App\Maintenance\Interpreter;

use App\Exception\InterpretActionNotSupportedException;
use App\Maintenance\Ioc\IoC;
use App\Queue\Queue;

/**
 * Интерпретатор заказов действий над игровым объектом.
 */
class Interpreter
{
    private const AUTH_KEEPER = 'authKeeper';
    private InterpretActionRegistry $registry;

    public function __construct(
        private readonly IoC $ioC,
        private readonly Queue $queue,
    ) {
        $this->registry = new InterpretActionRegistry();
    }

    /**
     * @throws InterpretActionNotSupportedException
     */
    public function interpret(InterpretObject $order): void
    {
        $this->toggleScope($order);

        $commandName = $this->registry->getCommand($order->getAction());
        /** @var InterpretCommandInterface $command */
        $command = new $commandName();

        $command->execute($this->ioC, $order, $this->queue);
    }

    private function toggleScope(InterpretObject $order): void
    {
        $this->ioC->resolve(IoC::SCOPES_CURRENT, self::AUTH_KEEPER);

        $playerScopeName = $this->ioC->resolve($order->getAuthToken());

        $this->ioC->resolve(IoC::SCOPES_CURRENT, $playerScopeName);
    }
}
