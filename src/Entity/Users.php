<?php

namespace App\Entity;

use App\Security\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

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
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * По количеству экспы будм определять уровень
     * @example level-1 <= 1 000 expa level-2 <= 2 000 expa...
     *
     * @ORM\Column(type="integer")
     */
    private $exp;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * Many Users have Many Users.
     * @ORM\ManyToMany(targetEntity="Users", mappedBy="followers")
     */
    private $subscribed_to;

    /**
     * Many Users have many Users.
     * followe_user_id - Мои подписчики
     * subscribed_to_user_id - На кого я подписан
     *
     * @ORM\ManyToMany(targetEntity="Users", inversedBy="subscribed_to")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="followe_user_id", referencedColumnName="id")}
     *      )
     */
    private $followers;

    /**
     * users_hobbies
     *
     * @var Tags[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Tags", cascade={"persist"})
     * @ORM\JoinTable(name="users_tags")
     * @ORM\OrderBy({"name": "ASC"})
     * @Constraints\Count(max="6", maxMessage="Максимум 6 увлечений")
     */
    private $hobbies;

    /**
     * @var Likes[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Likes",
     *      mappedBy="user",
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
     *      mappedBy="user",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     */
    private $dislikes;

    /**
     * @var UsersLogs[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="UsersLogs",
     *      mappedBy="users",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"created_at": "DESC"})
     */
    private $usersLogs;

    function __construct()
    {
        $this->f_name = '';
        $this->l_name = '';
        $this->role = 12;
        $this->status = 1;
        $this->created_at = new \DateTime();
        $this->points = 0;
        $this->exp = 0;

        $this->followers = new ArrayCollection();
        $this->subscribed_to = new ArrayCollection();

        $this->hobbies = new ArrayCollection();

        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
        $this->usersLogs = new ArrayCollection();
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

    public function getPoints()
    {
        return $this->points;
    }

    public function addPoints(int $points): self
    {
        $this->points = $this->points + $points;

        return $this;
    }

    public function getExp()
    {
        return $this->exp;
    }

    public function addExpa(int $expa): self
    {
        $this->exp = $this->exp + $expa;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }
    
    public function isFollowed(Users $author): bool
    {
        foreach ($this->getFollowers() as $follower) {
            if ($author->getId() == $follower->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFolloweTo(Users $follower): self
    {
        $this->subscribed_to->add($this);

        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }

        return $this;
    }

    public function removeFolloweTo(Users $follower): void
    {
        $this->followers->removeElement($follower);
    }

    public function getSubscribedTo(): Collection
    {
        return $this->subscribed_to;
    }

    public function getHobbies()
    {
        return $this->hobbies;
    }

    public function addHobby(Tags $hobbies)
    {
        if (!$this->hobbies->contains($hobbies)) {
            $this->hobbies->add($hobbies);
        }

        return $this;
    }

    public function removeHobby(Tags $hobbies): void
    {
        $this->hobbies->removeElement($hobbies);
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
        }

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
    }

    /**
     * @return Collection|UsersLogs[]
     */
    public function getUsersLogs(): Collection
    {
        return $this->usersLogs;
    }
}
