<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 */
class News
{
    public const
        //новое еще не проверили
        STATUS_IS_NEW = 30,
        //не прошло модерацию
        STATUS_IS_CANCEL = 20,
        //опубликовано
        STATUS_IS_PUBLISH = 10;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * Количество просмотров
     *
     * @ORM\Column(type="integer")
     */
    private $visitors;

    /**
     * Количество уникальных просмотров
     *
     * @ORM\Column(type="integer")
     */
    private $unique_visitors;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var Img $img;
     *
     * @ORM\OneToOne(targetEntity="Img")
     * @ORM\JoinColumn(name="img_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=355)
     */
    private $slug;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Comments",
     *      mappedBy="news",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"created_at": "DESC"})
     */
    private $comments;

    /**
     * @var Tags[]|ArrayCollection fetch="EAGER" Получает автоматически даные
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Tags", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="news_tags")
     * @ORM\OrderBy({"name": "ASC"})
     * @Constraints\Count(max="6", maxMessage="Максимум 6 тегов")
     */
    private $tags;

    /**
     * @var Likes[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Likes",
     *      mappedBy="news",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     */
    private $likes;

    /**
     * @var Dislikes[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Dislikes",
     *      mappedBy="news",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     */
    private $dislikes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $published_at;

    private $badge, $action;

    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->updated_at = new DateTime();
        $this->published_at = new DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();

        $this->visitors = 0;
        $this->unique_visitors = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?Users
    {
        return $this->author;
    }

    /**
     * @param Users $author
     * @return $this
     */
    public function setAuthor(Users $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    public function getVisitors(): int
    {
        return $this->visitors;
    }

    public function setVisitors($visitors): self
    {
        $this->visitors = $visitors;

        return $this;
    }

    public function getUniqueVisitors(): int
    {
        return $this->unique_visitors;
    }

    public function setUniqueVisitors($unique_visitors): self
    {
        $this->unique_visitors = $unique_visitors;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPublishedAt(): \DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(\DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): void
    {
        $comment->setNews($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    /**
     * @param Tags ...$tags
     */
    public function addTag(Tags ...$tags): self
    {
        foreach ($tags as $tag) {
            if (!$this->tags->contains($tag)) {
                $this->tags->add($tag);
            }
        }

        return $this;
    }

    public function removeTag(Tags $tag): void
    {
        $this->tags->removeElement($tag);
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addLike(Likes $like): void
    {
        $like->setNews($this);
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }
    }

    public function removeLike(Likes $like): void
    {
        $this->likes->removeElement($like);
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addDislike(Dislikes $dislikes): void
    {
        $dislikes->setNews($this);
        if (!$this->dislikes->contains($dislikes)) {
            $this->dislikes->add($dislikes);
        }
    }

    public function removeDislike(Dislikes $dislikes): void
    {
        $this->dislikes->removeElement($dislikes);
    }

    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    public function getBadges()
    {
        return $this->badge;
    }

    /**
     * @link https://getbootstrap.com/docs/4.0/components/badge/
     *
     * @param $text
     * @param string $class
     */
    public function addBadge($text, $class = 'badge-dark')
    {
        $this->badge[] = [
            'text' => $text,
            'class' => $class,
        ];
    }

    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->action;
    }

    /**
     * @param $text
     * @param $href
     * @return $this
     */
    public function addAction($text, $href): self
    {
        $this->action[] = [
            'text' => $text,
            'href' => $href,
        ];

        return  $this;
    }

    /**
     * @return string
     */
    public function getImg(): ?string
    {
        $img = $this->img;
        return $img ? $img->getUrl() : '';
    }

    /**
     * @return Img|null
     */
    public function getImgEntity(): ?Img
    {
        return $this->img;
    }

    /**
     * @param Img|null $img
     * @return $this
     */
    public function setImg(?Img $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return News
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
