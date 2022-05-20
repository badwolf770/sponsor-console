<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\Project\Infrastructure\Entity\Flight;
use App\Project\Infrastructure\Entity\Spot;
use App\Project\Infrastructure\Entity\TargetAudience;
use App\Tests\ApiTester;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Doctrine\Common\Collections\Criteria;

class FlightControllerCest
{
    private string $packageId = 'b5a6b463-31fb-451a-b3cc-a2229d485b83';
    private ?string $flightId = null;
    private ?string $targetAudienceId = null;

    public function _before(ApiTester $I)
    {
        ini_set('error_reporting', 0);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $audience = $I->grabEntityFromRepository(TargetAudience::class, [
            Criteria::create()->setMaxResults(1)
        ]);
        $this->targetAudienceId = $audience->getId();
    }

    /**
     * @dataProvider flightSuccessfulProvider
     * @throws \JsonException
     */
    public function testCreateSuccessfully(ApiTester $I, Example $example): void
    {
        $example['targetAudienceId'] = $this->targetAudienceId;
        $exampleData = $I->getExampleData($example);
        $I->sendPost("/package/$this->packageId/flight", $exampleData);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);
        /* @var Flight $flight */
        $flight = $I->grabEntityFromRepository(Flight::class, ['id' => $response['id']]);
        $this->flightId = $flight->getId();

        $this->compareFlightEntityAndData($I, $flight, $exampleData);
    }

    /**
     * @dataProvider flightFailProvider
     * @throws \JsonException
     */
    public function testCreateFail(ApiTester $I, Example $example): void
    {
        $exampleData = $I->getExampleData($example);
        $I->sendPost("/package/$this->packageId/flight", $exampleData);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \JsonException
     */
    public function testGetAllByPackage(ApiTester $I): void
    {
        $I->sendGet("/package/$this->packageId/flight");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);
        $I->assertNotEmpty($response);
        $I->assertArrayHasKey('flights', $response);
        foreach ($response['flights'] as $flight) {
            $I->assertArrayHasKey('id', $flight);
            $I->assertArrayHasKey('name', $flight);
            $I->assertArrayHasKey('targetAudience', $flight);
            $I->assertArrayHasKey('id', $flight['targetAudience']);
            $I->assertArrayHasKey('name', $flight['targetAudience']);
        }
    }

    public function testLink(ApiTester $I): void
    {
        $spotsIds = $this->getSpotIdsForLink();
        $I->sendPatch("/package/$this->packageId/flight/$this->flightId/spots", ['spotIds' => $spotsIds]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
        /* @var Spot[] $spots */
        $spots = $I->grabEntitiesFromRepository(Spot::class, ['flight' => $this->flightId]);
        $I->assertNotEmpty($spots);
        $I->assertIsArray($spots);
        foreach ($spots as $spot) {
            $I->assertContains($spot->getId(), $spotsIds);
        }
    }

    /**
     * @dataProvider linkFailProvider
     */
    public function testLinkFail(ApiTester $I, Example $example): void
    {
        $I->sendPatch("/package/$this->packageId/flight/$this->flightId/spots", ['spotIds' => $example['spotIds']]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    private function linkFailProvider(): array
    {
        return [
            [
                'spotIds' => [
                    'c32a4f2a-f61b-49e1-8a59-6acfe42ed263',
                    '75c3634b-5f22-4a3f-96bc-0d0f1754d6c5',
                ]
            ],
            [
                'spotIds' => [
                    ''
                ]
            ]
        ];
    }

    /**
     * @throws \JsonException
     */
    public function testDeleteFlight(ApiTester $I): void
    {
        foreach ($this->getSpotIdsForLink() as $spotId){
            $I->updateInDatabase('spot', ['flight_id' => null], ['id' => $spotId]);
        }
        $I->sendDelete("/package/$this->packageId/flight/$this->flightId");
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
        $I->dontSeeInRepository(Flight::class, ['id' => $this->flightId]);
    }

    private function compareFlightEntityAndData(ApiTester $I, Flight $flight, array $exampleData): void
    {
        $array = $I->normalize($flight);

        $I->assertArrayHasKey('targetAudience', $array);
        $I->assertArrayHasKey('id', $array['targetAudience']);
        $I->assertEquals($exampleData['targetAudienceId'], $array['targetAudience']['id']);

        $I->assertArrayHasKey('package', $array);
        $I->assertArrayHasKey('id', $array['package']);
        $I->assertEquals($exampleData['packageId'], $array['package']['id']);

        $I->assertArrayHasKey('name', $array);
        $I->assertEquals($exampleData['name'], $array['name']);


    }

    private function getSpotIdsForLink(): array
    {
        return [
            '3ea8883f-c6fa-4d76-aa1f-10bb975fa736',
            '8c1a2bcd-901b-4b5c-ba06-49dd385a271e',
            'e2d112eb-55e0-42df-a3a1-21cf0cb78f1e'
        ];
    }

    private function flightSuccessfulProvider(): array
    {
        return [
            [
                'packageId' => $this->packageId,
                'name' => 'name',
                'targetAudienceId' => '',
            ],
            [
                'packageId' => $this->packageId,
                'name' => 12312123,
                'targetAudienceId' => '',
            ]
        ];
    }

    private function flightFailProvider(): array
    {
        return [
            [
                'packageId' => '',
                'name' => '',
                'targetAudienceId' => '',
            ],
            [
                'packageId' => $this->packageId,
                'name' => '',
                'targetAudienceId' => '',
            ],
            [
                'packageId' => '',
                'name' => 12312123,
                'targetAudienceId' => '',
            ],
            [
                'packageId' => '',
                'name' => '',
                'targetAudienceId' => 'b5a6b463-31fb-451a-b3cc-a2229d485b83',
            ],
            [
                'packageId' => '4bcac993-1ae9-42bd-9b1c-5e84c579fd0a',
                'name' => '',
                'targetAudienceId' => '4bcac993-1ae9-42bd-9b1c-5e84c579fd0a',
            ],
            [
                'packageId' => 'b5a6b463-31fb-451a-b3cc-a2229d485b83',
                'name' => 'name',
                'targetAudienceId' => 'd88f8206-1700-4c3e-b1ec-45248009bd38',
            ],
        ];
    }
}