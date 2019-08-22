<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 */
class Comments
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var News
     *
     * @ORM\ManyToOne(targetEntity="News", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $news;

    /**
     * @ORM\Column(type="text")
     * @Constraints\NotBlank(message="Комментарий не может быть пустым")
     * @Constraints\Length(
     *     min=5,
     *     minMessage="Минимум 5 символов",
     *     max=10000,
     *     maxMessage="Максимум 10 000 символов"
     * )
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * @Constraints\IsTrue(message="Хватит спамить!")
     */
    public function isLegitComment(): bool
    {
        $containsInvalidCharacters = false !== mb_strpos($this->text, '@');

        return !$containsInvalidCharacters;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getAuthor(): ?Users
    {
        return $this->author;
    }

    public function setAuthor(Users $author)
    {
        $this->author = $author;

        return $this;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(News $news)
    {
        $this->news = $news;

        return $this;
    }
}
