<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\Project\Domain\ValueObject\CalculationStatus;
use App\Project\Domain\ValueObject\ExportStatus;
use App\Project\Infrastructure\Entity\Package;
use App\Tests\ApiTester;
use Codeception\Example;
use Codeception\Util\HttpCode;

class PackageControllerCest
{
    private string $packageId = 'b5a6b463-31fb-451a-b3cc-a2229d485b83';

    public function _before(ApiTester $I)
    {
        ini_set('error_reporting', 0);
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    /**
     * @example["true",1]
     * @example["false",0]
     */
    public function changeActiveSuccess(ApiTester $I, Example $example): void
    {
        $I->sendPatch("/project/3974e702-3b9e-4f19-bbe1-48b5e369cf77/package/$this->packageId/active/" . $example[0]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        /* @var Package $package */
        $package = $I->grabEntityFromRepository(Package::class, ['id' => $this->packageId]);
        $I->assertEquals((bool)$example[1], $package->getActive());
    }

    /**
     * @throws \JsonException
     */
    public function testGetById(ApiTester $I): void
    {
        $I->sendGet("/project/3974e702-3b9e-4f19-bbe1-48b5e369cf77/package/de5fadc7-9bc7-4465-acc2-b3d1b8d8b468");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);

        $I->assertNotEmpty($response);
        $I->assertArrayHasKey('id', $response);
        $I->assertArrayHasKey('name', $response);
        $I->assertArrayHasKey('tax', $response);
        $I->assertArrayHasKey('active', $response);
        $I->assertArrayHasKey('calculationStatus', $response);
        $I->assertContains($response['calculationStatus'], [
            CalculationStatus::NotCalculated->value,
            CalculationStatus::InProgress->value,
            CalculationStatus::Completed->value,
        ]);
        $I->assertArrayHasKey('channels', $response);
        $I->assertIsArray($response['channels']);
        foreach ($response['channels'] as $channel) {
            $I->assertArrayHasKey('id', $channel);
            $I->assertArrayHasKey('name', $channel);
            $I->assertArrayHasKey('months', $channel);
            $I->assertIsArray($channel['months']);
            foreach ($channel['months'] as $month) {
                $I->assertArrayHasKey('name', $month);
                $I->assertArrayHasKey('flights', $month);
                $I->assertIsArray($month['flights']);
                foreach ($month['flights'] as $flight) {
                    $I->assertArrayHasKey('id', $flight);
                    $I->assertArrayHasKey('name', $flight);
                    $I->assertArrayHasKey('reaches', $flight);
                    $I->assertArrayHasKey('ots', $flight);
                    $I->assertIsArray($flight['reaches']);
                    foreach ($flight['reaches'] as $reach) {
                        $I->assertArrayHasKey('id', $reach);
                        $I->assertArrayHasKey('name', $reach);
                        $I->assertArrayHasKey('value', $reach);
                    }
                    $I->assertArrayHasKey('programs', $flight);
                    $I->assertIsArray($flight['programs']);
                    foreach ($flight['programs'] as $program) {
                        $I->assertArrayHasKey('id', $program);
                        $I->assertArrayHasKey('name', $program);
                        $I->assertArrayHasKey('spots', $program);
                        $I->assertIsArray($program['spots']);
                        foreach ($program['spots'] as $spot) {
                            $I->assertArrayHasKey('id', $spot);
                            $I->assertArrayHasKey('sponsorType', $spot);
                            $I->assertArrayHasKey('weekDay', $spot);
                            $I->assertArrayHasKey('timingInSec', $spot);
                            $I->assertArrayHasKey('outsPerMonth', $spot);
                            $I->assertArrayHasKey('cost', $spot);
                            $I->assertArrayHasKey('broadcastStart', $spot);
                            $I->assertArrayHasKey('broadcastFinish', $spot);
                            $I->assertArrayHasKey('rating', $spot);
                            $I->assertNotEmpty($spot['rating']);
                            $I->assertArrayHasKey('id', $spot['rating']);
                            $I->assertArrayHasKey('tvr', $spot['rating']);
                            $I->assertArrayHasKey('grps20', $spot['rating']);
                            $I->assertArrayHasKey('avTvr', $spot['rating']);
                            $I->assertArrayHasKey('trps', $spot['rating']);
                            $I->assertArrayHasKey('trps20', $spot['rating']);
                            $I->assertArrayHasKey('cpp', $spot['rating']);
                            $I->assertArrayHasKey('affinity', $spot['rating']);
                        }
                    }
                }
            }
        }
    }

    public function testCalculateStatistics(ApiTester $I): void
    {
        $I->sendPost("/project/86377600-e5f0-400d-9906-ba08065816b2/package/bceaff62-3cf2-4f26-8f7b-2917109b68e0/calculate-statistics");
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }

    /**
     * @throws \JsonException
     */
    public function testGetStatusOfCalculateStatistics(ApiTester $I): void
    {
        $I->sendGet("/project/86377600-e5f0-400d-9906-ba08065816b2/package/bceaff62-3cf2-4f26-8f7b-2917109b68e0/calculate-statistics");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);

        $I->assertNotEmpty($response);
        $I->assertArrayHasKey('status', $response);
        $I->assertContains($response['status'], [
            CalculationStatus::NotCalculated->value,
            CalculationStatus::InProgress->value,
            CalculationStatus::Completed->value,
        ]);
    }
}