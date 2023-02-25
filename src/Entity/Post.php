<?php
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'posts')]
final class Post
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', nullable: false)]
    private string $title;

    #[Column(type: 'text', nullable: false)]
    private string $content;

    #[Column(type: 'string', nullable: true)]
    private string $featuredImage;

    #[Column(type: 'boolean', nullable: false)]
    private string $isPublished;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Post
     */
    public function setId(int $id): Post
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Post
     */
    public function setTitle(string $title): Post
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Post
     */
    public function setContent(string $content): Post
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getFeaturedImage(): string
    {
        return $this->featuredImage;
    }

    /**
     * @param string $featuredImage
     * @return Post
     */
    public function setFeaturedImage(string $featuredImage): Post
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }

    /**
     * @return string
     */
    public function getIsPublished(): string
    {
        return $this->isPublished;
    }

    /**
     * @param string $isPublished
     * @return Post
     */
    public function setIsPublished(string $isPublished): Post
    {
        $this->isPublished = $isPublished;
        return $this;
    }
}