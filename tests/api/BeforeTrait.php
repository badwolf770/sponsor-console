<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\Project\Infrastructure\Entity\Project;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

trait BeforeTrait
{
    protected ?string $projectId = null;

    /**
     * @throws \JsonException
     */
    public function _before(ApiTester $I)
    {
        if (is_null($this->projectId)) {

            $I->sendPost('/project', [
                'name' => 'test',
                'client' => 'test',
                'brand' => 'test',
                'reaches' => [1, 2, 3],
            ]);
            $I->seeResponseCodeIs(HttpCode::CREATED);
            $I->seeResponseIsJson();
            $projectArrayFromResponse = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);
            /* @var Project $project */
            $project = $I->grabEntityFromRepository(Project::class, ['id' => $projectArrayFromResponse['id']]);
            $this->projectId = $project->getId();
        }
    }
}
