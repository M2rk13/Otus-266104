<?php

namespace App\Tests\SpaceShipGameTests;

use App\AuthService\AuthService;
use App\AuthService\GameServer;
use App\Exception\AuthException;
use Firebase\JWT\SignatureInvalidException;
use JsonException;
use PHPUnit\Framework\TestCase;

class StartGameTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testSuccessLogin(): void
    {
        $testPlayerId = '105';

        $playerIdList = [
            $testPlayerId,
            '123',
            '124',
        ];

        $gameServer = new GameServer();
        $groupId = $gameServer->register($playerIdList);

        $jwt = $gameServer->login($testPlayerId);
        $gameServer->checkAuthorization($jwt);

        $payload = (new AuthService())->decodeJwt($jwt, 'some_key');

        $expectedPayload = [
            'groupId' => $groupId,
            'playerId' => $testPlayerId,
        ];

        $this->assertEquals($expectedPayload, $payload['data']);
    }

    public function testWrongPlayerId(): void
    {
        $testPlayerId = '105';

        $playerIdList = [
            '122',
            '123',
            '124',
        ];

        $gameServer = new GameServer();
        $groupId = $gameServer->register($playerIdList);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage(sprintf(
            'there is no Player with ID [105] in PlayerGroup with ID [%s]',
            $groupId
        ));

        $gameServer->login($testPlayerId);
    }

    /**
     * @throws JsonException
     */
    public function testSignatureFailure(): void
    {
        $testPlayerId = '105';

        $playerIdList = [
            $testPlayerId,
            '123',
            '124',
        ];

        $gameServer = new GameServer();
        $gameServer->register($playerIdList);
        $jwt = $gameServer->login($testPlayerId);

        $this->expectException(SignatureInvalidException::class);

        $gameServer->checkAuthorization($jwt . 'test');
    }
}
