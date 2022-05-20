<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class UserControllerCest
{
    public function _before(ApiTester $I)
    {
        ini_set('error_reporting', 0);
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    /**
     * @throws \JsonException
     */
    public function testGetCurrent(ApiTester $I): void
    {
        $I->sendGet("/user/current");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);
        $I->assertNotEmpty($response);

        $I->assertArrayHasKey('id', $response);
        $I->assertArrayHasKey('name', $response);
        $I->assertArrayHasKey('surname', $response);
        $I->assertArrayHasKey('email', $response);
        $I->assertArrayHasKey('roles', $response);
        $I->assertIsArray($response['roles']);

    }
}