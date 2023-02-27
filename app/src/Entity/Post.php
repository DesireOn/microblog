<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[Entity, Table(name: 'posts')]
final class Post
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Column(type: 'string', nullable: false)]
    private ?string $title = null;

    #[Column(type: 'text', nullable: false)]
    private ?string $content = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $featuredImage = null;

    #[Column(type: 'boolean', nullable: false)]
    private ?bool $isPublished = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return Post
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Post
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage;
    }

    /**
     * @param string|null $featuredImage
     * @return Post
     */
    public function setFeaturedImage(?string $featuredImage): self
    {
        $this->featuredImage = $featuredImage;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool $isPublished
     * @return Post
     */
    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('title', new Assert\NotBlank());
        $metadata->addPropertyConstraint('content', new Assert\NotBlank());
        $metadata->addPropertyConstraint('isPublished', new Assert\Type('boolean'));
    }
}