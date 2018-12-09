<?php

namespace App\Tests\UI\Rest\Controller;


use App\Domain\Article\Article;
use App\Domain\Article\File;

class ArticleControllerTest extends ApiTestCase
{

    private const TITLE = 'Test Title';
    private const AUTHOR = 'Test Author';
    private const BODY = <<<EOF
Spicy **jalapeno bacon** ipsum dolor amet veniam shank in dolore. Ham hock nisi landjaeger cow,
lorem proident [beef ribs](https://baconipsum.com/) aute enim veniam ut cillum pork chuck picanha. Dolore reprehenderit
labore minim pork belly spare ribs cupim short loin in. Elit exercitation eiusmod dolore cow
**turkey** shank eu pork belly meatball non cupim.
EOF;
    private const FILE_NAME = 'TestFileName.jpg';

    public function testGetArticles()
    {
        $this->createArticle();
        $this->get('/api/articles');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateArticle()
    {
        $article = $this->createArticle();
        $this->put('/api/articles/'. $article->getId(), [
            'title' => 'new title',
            'author' => 'new author',
            'body' => 'new body',
        ]);
        $response = $this->client->getResponse();
        self::assertSame(201, $response->getStatusCode());
        self::assertEquals($this->asserter()->readResponseProperty($response, 'title'), 'new title');
        $this->asserter()->assertResponsePropertiesExist(
            $response,
            ['id', 'title', 'author', 'body', 'file.name', 'file.url']
        );
    }
    /*
    public function testDeleteArticle()
    {

    }
    */
    public function testCreateArticle()
    {
        $this->post('/api/articles', [
            'title' => self::TITLE,
            'author' => self::AUTHOR,
            'body' => self::BODY,
            'file_name' => self::FILE_NAME
        ]);
        $response = $this->client->getResponse();
        self::assertSame(201, $response->getStatusCode());
        self::assertEquals($this->asserter()->readResponseProperty($response, 'file.name'), self::FILE_NAME);
        $this->asserter()->assertResponsePropertiesExist(
            $response,
            ['id', 'title', 'author', 'body', 'file.name', 'file.url']
        );
    }


    protected function createArticle(string $title = self::TITLE, string $author = self::AUTHOR, string $body = self::BODY): Article
    {
        $article = new Article();
        $article->setTitle($title);
        $article->setBody($body);
        $article->setAuthor($author);
        $article->setFile(new File(self::FILE_NAME));

        $em = $this->getEntityManager();
        $em->persist($article);
        $em->flush();

        return $article;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->createUser();
        $this->auth();
    }
}
