<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowersRepository")
 */
class Followers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $followe;

    /**
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollowe(): ?int
    {
        return $this->followe;
    }

    public function setFollowe(int $followe): self
    {
        $this->followe = $followe;

        return $this;
    }

    public function getSubscription(): ?int
    {
        return $this->subscription;
    }

    public function setSubscription(int $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }
}
