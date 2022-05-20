<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class TargetAudienceControllerCest
{
    public function _before(ApiTester $I)
    {
        ini_set('error_reporting', 0);
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    /**
     * @throws \JsonException
     */
    public function testFindByName(ApiTester $I): void
    {
        $I->sendGet("/target-audience/All14");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);
        $I->assertNotEmpty($response);
        $I->assertArrayHasKey('targetAudiences', $response);
        foreach ($response['targetAudiences'] as $targetAudience) {
            $I->assertArrayHasKey('id', $targetAudience);
            $I->assertArrayHasKey('name', $targetAudience);
        }
    }
}