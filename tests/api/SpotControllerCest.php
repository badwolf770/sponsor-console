<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\Project\Infrastructure\Entity\Spot;
use App\Tests\ApiTester;
use Codeception\Example;
use Codeception\Util\HttpCode;

class SpotControllerCest
{
    private string $spotId = '3a45c06b-61f4-4950-bac1-bed79207a0c1';
    private string $flightId = '5612b9ce-c76f-408f-a3f2-7b58d0e4169b';

    public function _before(ApiTester $I)
    {
        ini_set('error_reporting', 0);
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    public function testLink(ApiTester $I): void
    {
        $I->sendPatch("/spot/$this->spotId/flight/$this->flightId/link");
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
        /* @var Spot $spot */
        $spot = $I->grabEntityFromRepository(Spot::class, ['id' => $this->spotId]);
        $I->assertNotEmpty($spot);
        $I->assertNotEmpty($spot->getFlight());
        $I->assertEquals($this->flightId, $spot->getFlight()->getId());
    }


    /**
     * @dataProvider linkFailProvider
     * @throws \JsonException
     */
    public function testLinkFail(ApiTester $I, Example $example): void
    {
        $I->sendPatch("/spot/{$example['spotId']}/flight/{$example['flightId']}/link");
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @dataProvider changeRatingProvider
     * @throws \JsonException
     */
    public function testChangeRating(ApiTester $I, Example $example): void
    {
        $I->sendPut("/spot/{$example['spotId']}/rating/{$example['ratingId']}", $example['rating']);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
        /* @var Spot $spot */
        $spot = $I->grabEntityFromRepository(Spot::class, ['id' => $example['spotId']]);
        $I->assertNotEmpty($spot);
        $I->assertNotEmpty($spot->getRating());
        $I->assertEquals($example['ratingId'], $spot->getRating()->getId());
        $ratingArray = $I->normalize($spot->getRating());
        foreach ($example['rating'] as $ratingItem => $value) {
            $I->assertEquals($value, $ratingArray[$ratingItem]);
        }
    }

    /**
     * @dataProvider changeRatingProviderFail
     * @throws \JsonException
     */
    public function testChangeRatingFail(ApiTester $I, Example $example): void
    {
        $I->sendPut("/spot/{$example['spotId']}/rating/{$example['ratingId']}", $example['rating']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    private function changeRatingProvider(): array
    {
        return [
            [
                'spotId' => '152092b2-727b-4c83-bbf1-16974f5bcb45',
                'ratingId' => '754f1322-a50b-4a5b-a759-608c8d75d466',
                'rating' => [
                    'tvr' => 0.2,
                    'grps20' => 0.2,
                    'affinity' => 0.2,
                ],
            ],
            [
                'spotId' => '152092b2-727b-4c83-bbf1-16974f5bcb45',
                'ratingId' => '754f1322-a50b-4a5b-a759-608c8d75d466',
                'rating' => [
                ],
            ],
            [
                'spotId' => '152092b2-727b-4c83-bbf1-16974f5bcb45',
                'ratingId' => '754f1322-a50b-4a5b-a759-608c8d75d466',
                'rating' => [
                    'tvr' => 0,
                    'grps20' => 0,
                    'affinity' => 0,
                ],
            ],
            [
                'spotId' => '152092b2-727b-4c83-bbf1-16974f5bcb45',
                'ratingId' => '754f1322-a50b-4a5b-a759-608c8d75d466',
                'rating' => [
                    'tvr' => 20,
                    'grps20' => 20.22,
                    'affinity' => 2522.2222,
                ],
            ],
            [
                'spotId' => '152092b2-727b-4c83-bbf1-16974f5bcb45',
                'ratingId' => '754f1322-a50b-4a5b-a759-608c8d75d466',
                'rating' => [
                    'tvr' => 20,
                ],
            ],
            [
                'spotId' => '152092b2-727b-4c83-bbf1-16974f5bcb45',
                'ratingId' => '754f1322-a50b-4a5b-a759-608c8d75d466',
                'rating' => [
                    'grps20' => 20.22,
                ],
            ],
            [
                'spotId' => '152092b2-727b-4c83-bbf1-16974f5bcb45',
                'ratingId' => '754f1322-a50b-4a5b-a759-608c8d75d466',
                'rating' => [
                    'affinity' => 2522.2222,
                ],
            ]
        ];
    }

    private function changeRatingProviderFail(): array
    {
        return [
            [
                // спот и рейтинг не относятся друг к другу
                'spotId' => '5e22bb39-70a5-4f95-835a-add98aa7b494',
                'ratingId' => 'a136df76-80b1-4748-b8f5-189eada6a58d',
                'rating' => [
                    'tvr' => 0,
                    'grps20' => 0,
                    'affinity' => 0,
                ],
            ],
            [
                // спот не существует
                'spotId' => 'c34e83b5-0eba-4cb5-b6f0-80959661bc5c',
                'ratingId' => 'a136df76-80b1-4748-b8f5-189eada6a58d',
                'rating' => [
                    'tvr' => 0,
                    'grps20' => 0,
                    'affinity' => 0,
                ],
            ],
            [
                // рейтинг ид не существует
                'spotId' => '5e22bb39-70a5-4f95-835a-add98aa7b494',
                'ratingId' => 'c34e83b5-0eba-4cb5-b6f0-80959661bc5c',
                'rating' => [
                    'tvr' => 0,
                    'grps20' => 0,
                    'affinity' => 0,
                ],
            ]
        ];
    }

    private function linkFailProvider(): array
    {
        return [
            [
                'spotId' => '9b45a3c0-d8af-4765-a259-d37541f7e9aa',
                'flightId' => '5612b9ce-c76f-408f-a3f2-7b58d0e4169b'
            ],
        ];
    }
}