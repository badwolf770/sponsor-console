<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\Project\Application\TemplateImport\TemplateType;
use App\Project\Domain\ValueObject\ExportStatus;
use App\Project\Infrastructure\Entity\Project;
use App\Tests\ApiTester;
use Codeception\Example;
use Codeception\Util\HttpCode;

class ProjectControllerCest
{
    private ?string $projectId = null;

    public function _before(ApiTester $I)
    {
        ini_set('error_reporting', 0);
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    /**
     * @dataProvider projectSuccessfulProvider
     * @throws \JsonException
     */
    public function testCreateSuccessfully(ApiTester $I, Example $example): void
    {
        $exampleData = $I->getExampleData($example);
        $I->sendPost('/project', $exampleData);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $response = $I->grabResponse();
        $projectArrayFromResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        /* @var Project $project */
        $project = $I->grabEntityFromRepository(Project::class, ['id' => $projectArrayFromResponse['id']]);
        $this->projectId = $project->getId();

        $this->compareProjectAndData($I, $project, $exampleData);
    }

    /**
     * @dataProvider projectNotSuccessfulProvider
     * @param ApiTester $I
     * @param Example $example
     */
    public function testCreateNotSuccessfully(ApiTester $I, Example $example): void
    {
        $exampleData = $I->getExampleData($example);
        $I->sendPost('/project', $exampleData);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @dataProvider projectSuccessfulProvider
     * @throws \JsonException
     */
    public function testUpdateSuccessfully(ApiTester $I, Example $example): void
    {
        $projectData = $I->getExampleData($example);
        $I->sendPut("/project/{$this->projectId}", $projectData);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $projectData['id'] = $this->projectId;
        /* @var Project $project */
        $project = $I->grabEntityFromRepository(Project::class, ['id' => $this->projectId]);
        $this->compareProjectAndData($I, $project, $projectData);
    }

    /**
     * @dataProvider projectNotSuccessfulProvider
     * @throws \JsonException
     */
    public function testUpdateNotSuccessfully(ApiTester $I, Example $example): void
    {
        $projectData = $I->getExampleData($example);
        $I->sendPut("/project/{$this->projectId}", $projectData);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @dataProvider projectTemplatesProvider
     * @throws \JsonException
     */
    public function testUploadTemplate(ApiTester $I, Example $example): void
    {
        $I->haveHttpHeader('Content-Type', 'multipart/form-data');
        copy(codecept_data_dir($example['file']), codecept_data_dir($example['tmpName']));

        $I->sendPost("/project/{$this->projectId}/template/{$example['template']}",
            [],
            [
                'file' => codecept_data_dir($example['tmpName'])
            ]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        /* @var Project $project */
        $project = $I->grabEntityFromRepository(Project::class, ['id' => $this->projectId]);
        $I->assertNotEmpty($project->getTemplateImports());
        foreach ($project->getTemplateImports() as $import) {
            $I->assertFileExists($import->getFilePath());
        }
    }

    /**
     * @throws \JsonException
     */
    public function testGetUploadStatus(ApiTester $I): void
    {
        $I->sendGet("/project/{$this->projectId}/upload-status");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);

        $I->assertNotEmpty($response);
        $I->assertArrayHasKey('statuses', $response);
        foreach ($response['statuses'] as $status) {
            $I->assertArrayHasKey('fileName', $status);
            $I->assertArrayHasKey('percent', $status);
        }
    }

    /**
     * @throws \JsonException
     */
    public function testExport(ApiTester $I): void
    {
        $messagesCount = $I->grabFromDatabase('messenger_messages', 'COUNT(*)');
        $I->sendPost("/project/{$this->projectId}/export");
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $newMessagesCount = $I->grabFromDatabase('messenger_messages', 'COUNT(*)');
        $I->assertEquals($messagesCount + 1, $newMessagesCount);
    }

    /**
     * @throws \JsonException
     */
    public function testGetProjects(ApiTester $I): void
    {
        $I->sendGet("/project");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);

        $I->assertNotEmpty($response);
        $I->assertArrayHasKey('projects', $response);
        foreach ($response['projects'] as $project) {
            $I->assertArrayHasKey('id', $project);
            $I->assertArrayHasKey('name', $project);
            $I->assertArrayHasKey('client', $project);
            $I->assertArrayHasKey('brand', $project);
            $I->assertArrayHasKey('exportFile', $project);
            $I->assertArrayHasKey('exportStatus', $project);
            $I->assertContains($project['exportStatus'], [
                ExportStatus::NotExported->value,
                ExportStatus::InProgress->value,
                ExportStatus::Completed->value,
            ]);
            if (!is_null($project['exportFile'])) {
                $I->assertArrayHasKey('id', $project['exportFile']);
                $I->assertArrayHasKey('name', $project['exportFile']);
                $I->assertArrayHasKey('webPath', $project['exportFile']);
            }
            $I->assertArrayHasKey('uploadStatuses', $project);
            $I->assertNotEmpty($project['uploadStatuses']);
            $I->assertArrayHasKey('statuses', $project['uploadStatuses']);
            foreach ($project['uploadStatuses']['statuses'] as $status) {
                $I->assertArrayHasKey('fileName', $status);
                $I->assertArrayHasKey('percent', $status);
            }
            $I->assertArrayHasKey('createdAt', $project);
            $I->assertArrayHasKey('reaches', $project);
            $I->assertIsArray($project['reaches']);
            foreach ($project['reaches'] as $reach) {
                $I->assertIsInt($reach);
            }
        }
    }

    /**
     * @throws \JsonException
     */
    public function testGetById(ApiTester $I): void
    {
        $I->sendGet("/project/3974e702-3b9e-4f19-bbe1-48b5e369cf77");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);

        $I->assertNotEmpty($response);
        $I->assertArrayHasKey('id', $response);
        $I->assertArrayHasKey('name', $response);
        $I->assertArrayHasKey('client', $response);
        $I->assertArrayHasKey('brand', $response);
        $I->assertArrayHasKey('exportFile', $response);
        $I->assertContains($response['exportStatus'], [
            ExportStatus::NotExported->value,
            ExportStatus::InProgress->value,
            ExportStatus::Completed->value,
        ]);
        if (!is_null($response['exportFile'])) {
            $I->assertArrayHasKey('id', $response['exportFile']);
            $I->assertArrayHasKey('name', $response['exportFile']);
            $I->assertArrayHasKey('webPath', $response['exportFile']);
        }
        $I->assertArrayHasKey('uploadStatuses', $response);
        $I->assertNotEmpty($response['uploadStatuses']);
        $I->assertArrayHasKey('statuses', $response['uploadStatuses']);
        foreach ($response['uploadStatuses']['statuses'] as $status) {
            $I->assertArrayHasKey('fileName', $status);
            $I->assertArrayHasKey('percent', $status);
        }
        $I->assertArrayHasKey('createdAt', $response);
        $I->assertArrayHasKey('reaches', $response);
        $I->assertIsArray($response['reaches']);
        foreach ($response['reaches'] as $reach) {
            $I->assertIsInt($reach);
        }
        $I->assertArrayHasKey('packages', $response);
        $I->assertIsArray($response['packages']);
        foreach ($response['packages'] as $package) {
            $I->assertArrayHasKey('id', $package);
            $I->assertArrayHasKey('name', $package);
            $I->assertArrayHasKey('tax', $package);
            $I->assertArrayHasKey('active', $package);
        }

    }

    /**
     * @throws \JsonException
     */
    public function testDeleteProject(ApiTester $I): void
    {
        $I->sendDelete("/project/$this->projectId");
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $I->dontSeeInRepository(Project::class, ['id' => $this->projectId]);
    }

    private function compareProjectAndData(ApiTester $I, Project $project, array $projectData): void
    {
        $projectArray = $I->normalize($project);
        foreach ($projectData as $key => $value) {
            $I->assertArrayHasKey($key, $projectArray);
            if ($key === 'reaches') {
                foreach ($value as $item) {
                    $I->assertContains($item, $projectArray[$key]);
                }
            } else {
                $I->assertEquals($value, $projectArray[$key]);
            }
        }
    }

    private function projectTemplatesProvider(): array
    {
        return [
            [
                'template' => TemplateType::EVEREST,
                'file' => 'template_types/everest.xlsx',
                'tmpName' => 'template_types/everest1.xlsx',
                'mimeTypes' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            ]
        ];
    }

    private function projectSuccessfulProvider(): array
    {
        return [
            [
                'name' => 'test',
                'client' => 'test',
                'brand' => 'test',
                'reaches' => [1, 2, 3],
            ],
            [
                'name' => 'qeqweqweqw',
                'client' => 'adsadasdsdas',
                'brand' => 'teasdsadqwst',
                'reaches' => [1, 2, 3, 7, 8, 234, 23423423423, 32423],
            ],
            [
                'name' => 123123123,
                'client' => 123123123,
                'brand' => 1231.2,
                'reaches' => [1, 2, 3, 7, 8, 234, 23423423423, 32423],
            ],
        ];
    }

    private function projectNotSuccessfulProvider(): array
    {
        return [
            [
                'name' => '',
                'client' => '',
                'brand' => '',
                'reaches' => [],
            ],
            [
                'name' => '213123',
                'client' => '',
                'brand' => '',
                'reaches' => [],
            ],
            [
                'name' => '',
                'client' => '1232',
                'brand' => '',
                'reaches' => [],
            ],
            [
                'name' => '',
                'client' => '',
                'brand' => '122323',
                'reaches' => [],
            ],
            [
                'name' => '',
                'client' => '',
                'brand' => '',
                'reaches' => [],
            ],
            [
                'name' => '',
                'client' => '',
                'brand' => '',
                'reaches' => [1, 2, 3, 4, 5],
            ],
            [
                'name' => 123123123,
                'client' => 123123123,
                'brand' => 1231.2,
                'reaches' => ['1', '2'],
            ],

        ];
    }
}