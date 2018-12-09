<?php
namespace App\Domain\Article;


class File
{
    private $name;

    /**
     * File constructor.
     * @param $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): ?string
    {
        return $this->getName() ? 'http://127.0.0.1:8081/files/' . $this->getName() : null;
    }
}
