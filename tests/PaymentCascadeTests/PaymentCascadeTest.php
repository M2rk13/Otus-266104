<?php

declare(strict_types=1);

namespace App\Tests\PaymentCascadeTests;

use App\Maintenance\Ioc\IoC;
use App\ProjectCascade\Command\RabbitDefinitionUpdateCommand;
use App\ProjectCascade\DBMaintence\DBManager;
use App\ProjectCascade\Enum\CascadeTransactionStatusEnum;
use App\ProjectCascade\Enum\QueueEnum;
use App\ProjectCascade\Enum\TransactionStatusEnum;
use App\ProjectCascade\GateWay\Controller\GatewayController;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeConsumer;
use App\ProjectCascade\RabbitMQ\Cascade\CascadeDefinition;
use App\ProjectCascade\RabbitMQ\RabbitClient;
use Exception;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class PaymentCascadeTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function __construct(string $name)
    {
        $GLOBALS['provider_list'] = require './provider-autoload.php';
        $isDocker = (bool) shell_exec('printenv IS_DOCKER');

        $GLOBALS['rabbit_host'] = '127.0.0.1';
        $GLOBALS['mysql_host'] = '127.0.0.1';

        if ($isDocker) {
            $GLOBALS['rabbit_host'] = 'rabbitmq.proj';
            $GLOBALS['mysql_host'] = 'mysql.proj';
        }

        if (isset($GLOBALS['IoC']) === false) {
            $GLOBALS['IoC'] = new IoC();
        }

        $cmd = new RabbitDefinitionUpdateCommand([new CascadeDefinition()]);
        $cmd->execute();

        parent::__construct($name);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testCascadeFailed(): void
    {
        $playerToken = 'ekjnvejknvkjvn';

        $request = new Request(
            method: 'POST',
            uri: 'payment',
            headers: ['auth' => $playerToken],
            body: '{"amount": 100, "paymentDetails": {"code": 124}, "providerName":"cascade"}'
        );

        $controller = new GatewayController();
        $response = $controller->gateway($request);
        $statusCode = $response->getStatusCode();

        $rabbitClient = new RabbitClient();
        $consumer = new CascadeConsumer();

        sleep(2);

        $messageList = [];

        do {
            $message = $rabbitClient->get(QueueEnum::CASCADE_QUEUE);
            $messageList[] = $message;
        } while ($message !== null);

        $consumer->process(array_filter($messageList));

        $transactionId = $this->getTransactionId();

        $body = sprintf('{"transactionId": "%s", "status": "%s"}', $transactionId, TransactionStatusEnum::CANCEL);

        $request = new Request(
            method: 'POST',
            uri: 'callback',
            body: $body
        );

        $controller->gateway($request);

        sleep(2);

        $messageList = [];

        do {
            $message = $rabbitClient->get(QueueEnum::CASCADE_QUEUE);
            $messageList[] = $message;
        } while ($message !== null);

        $consumer->process(array_filter($messageList));

        sleep(1);

        $transactionId = $this->getTransactionId();

        $body = sprintf('{"transactionId": "%s", "status": "%s"}', $transactionId, TransactionStatusEnum::CANCEL);

        $request = new Request(
            method: 'POST',
            uri: 'callback',
            body: $body
        );

        $controller->gateway($request);

        $managerDB = new DBManager();

        $qb = $managerDB->getQueryBuilder();

        $qb
            ->select('1')
            ->from('TransactionCascade', 'tc')
            ->where('tc.status = :doneCascadeStatus')
            ->setParameter('doneCascadeStatus', CascadeTransactionStatusEnum::DONE)
        ;

        $result = $managerDB->fetchOne($qb);

        self::assertSame($statusCode, 201);
        self::assertFalse($result);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testCascadeSuccess(): void
    {
        $playerToken = 'ekjnvejknvkjvn';

        $request = new Request(
            method: 'POST',
            uri: 'payment',
            headers: ['auth' => $playerToken],
            body: '{"amount": 100, "paymentDetails": {"code": 124}, "providerName":"cascade"}'
        );

        $controller = new GatewayController();
        $response = $controller->gateway($request);
        $statusCode = $response->getStatusCode();

        $rabbitClient = new RabbitClient();
        $consumer = new CascadeConsumer();

        sleep(2);

        $messageList = [];

        do {
            $message = $rabbitClient->get(QueueEnum::CASCADE_QUEUE);
            $messageList[] = $message;
        } while ($message !== null);

        $consumer->process(array_filter($messageList));

        $transactionId = $this->getTransactionId();

        $body = sprintf('{"transactionId": "%s", "status": "%s"}', $transactionId, TransactionStatusEnum::DONE);

        $request = new Request(
            method: 'POST',
            uri: 'callback',
            body: $body
        );

        $controller->gateway($request);

        sleep(1);

        $managerDB = new DBManager();

        $qb = $managerDB->getQueryBuilder();

        $qb
            ->select('1')
            ->from('TransactionCascade', 'tc')
            ->where('tc.status = :doneCascadeStatus')
            ->setParameter('doneCascadeStatus', CascadeTransactionStatusEnum::DONE)
        ;

        $result = $managerDB->fetchOne($qb);

        self::assertSame($statusCode, 201);
        self::assertNotFalse($result);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getTransactionId(): ?string
    {
        $managerDB = new DBManager();

        $qb = $managerDB->getQueryBuilder();

        $qb
            ->select('tc.currentTransactionId')
            ->from('TransactionCascade', 'tc')
            ->innerJoin('tc', 'Transaction', 't', 't.id = tc.currentTransactionId')
            ->where('tc.status = :waitCascadeStatus')
            ->andWhere('t.status = :waitStatus')
            ->orderBy('tc.createdAt', 'DESC')
            ->setParameter('waitCascadeStatus', CascadeTransactionStatusEnum::WAIT)
            ->setParameter('waitStatus', TransactionStatusEnum::WAIT)
        ;

        $result = (string) $managerDB->fetchOne($qb);

        return $result ?: null;
    }
}
