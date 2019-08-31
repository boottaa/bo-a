<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\LikesRepository")
 */
class Likes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var News
     *
     * @ORM\ManyToOne(targetEntity="News", inversedBy="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $news;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNews(): News
    {
        return $this->news;
    }

    public function setNews(News $news): self
    {
        $this->news = $news;

        return $this;
    }

}
