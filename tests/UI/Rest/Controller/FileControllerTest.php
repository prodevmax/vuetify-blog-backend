<?php
namespace App\Tests\UI\Rest\Controller;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileControllerTest extends ApiTestCase
{
    private $testPath;
    private $uploadPath;
    private $fileName;

    public function testUploadSuccess()
    {
        $this->client->request(
            'POST',
            '/api/files/upload',
            [],
            [ 'file' => new UploadedFile(
                $this->testPath . '/image.jpeg',
                filesize($this->testPath . '/image.jpeg'),
                'image/jpeg',
                0,
                true
            )],
            [
                'CONTENT_TYPE' => 'multipart/form-data',
                'HTTP_Authorization' => sprintf('Bearer %s', $this->token)
            ]

        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content = $response->getContent());
        $this->asserter()->assertResponsePropertyExists(
            $this->client->getResponse(),
            'file_name'
        );
        $this->fileName = $this->asserter()->readResponseProperty($response, 'file_name');
        $this->assertFileExists($this->uploadPath . '/' .  $this->fileName);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->testPath = realpath('var/test');
        $this->initTestFiles();
        $this->uploadPath = static::$kernel->getContainer()->getParameter('files_upload_path');
        $this->createUser();
        $this->auth();
    }

    protected function tearDown()
    {
        $this->clearTestFiles($this->fileName);
        parent::tearDown();
    }

    protected function initTestFiles(): void
    {
        if (file_exists($this->testPath . '/image.jpeg')) {
            unlink($this->testPath . '/image.jpeg');
        }
        copy(__DIR__ . '/../../../data/image.jpeg', $this->testPath . '/image.jpeg');
    }

    private function clearTestFiles(string $fileName): void
    {
        if (file_exists($this->uploadPath . '/'. $fileName)) {
            unlink($this->uploadPath . '/'. $fileName);
        }
    }
}