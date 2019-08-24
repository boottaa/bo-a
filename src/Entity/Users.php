<?php

namespace App\Entity;

use App\Security\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $f_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $l_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $ref_hash;

    /**
     * users.id пользователя который пригласил
     *
     * @ORM\Column(type="integer")
     */
    private $invited;

    /**
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var Followers[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Followers", cascade={"persist"})
     * @ORM\JoinTable(name="users_followers")
     * @ORM\OrderBy({"name": "ASC"})
     */
    private $followers;

    function __construct()
    {
        $this->f_name = '';
        $this->l_name = '';
        $this->role = 1;
        $this->status = 1;
        $this->created_at = new \DateTime();
        $this->followers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFName(): ?string
    {
        return $this->f_name;
    }

    public function setFName(?string $f_name): self
    {
        $this->f_name = $f_name;

        return $this;
    }

    public function getLName(): ?string
    {
        return $this->l_name;
    }

    public function setLName(?string $l_name): self
    {
        $this->l_name = $l_name;

        return $this;
    }

    public function getRefHash(): ?string
    {
        return $this->ref_hash;
    }

    public function setRefHash(string $ref_hash): self
    {
        $this->ref_hash = $ref_hash;

        return $this;
    }

    public function getInvited(): ?string
    {
        return $this->invited;
    }

    public function setInvited(int $invited = null): self
    {
        $this->invited = $invited;

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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

    /**
     * @inheritdoc
     */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->login, $this->password]);
    }
    /**
     * @inheritdoc
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->login, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        $result = [];
        $securityRole = new Role();
        foreach (str_split($this->getRole()) as $role) {
            $result[] = $securityRole->get($role);
        };

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->getLogin();
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
}}
