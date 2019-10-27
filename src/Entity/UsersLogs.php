<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersLogsRepository")
 */
class UsersLogs
{
    public const
        T_ADD_NEWS = 'Добавилена новость: %s. Вам начислено %s опыта.',

        T_GET_NEW_SUBSCRIBE = 'На вас подписался: %s. Вам начислено %s опыта.',
        T_NEW_SUBSCRIBE = 'Вы подписались на: %s. Вам начислено %s опыта.',

        T_GET_NEW_REFERAL = 'У вас новый реферал: %s. Вам начисленно %s опыта.',
        T_NEW_REFERAL = 'Вы зарегестрировались по реферальной ссылки пользователя: %s. Вам начисленно %s опыта.';

//        T_GET_NEW_LIKE = 'Получили лайк для новость: %s. Вам начислено %s опыта.',
//        T_NEW_LIKE = 'Поставил лайк для новости: %s. Вам начислено %s опыта.',
//
//        T_GET_NEW_DISLIKE = 'Получили дизлайк для новости: %s. Вам начислено %s опыта.',
//        T_NEW_DISLIKE = 'Поставил дизлайк для новости: %s. Вам начислено %s опыта.';

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
    private $user;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $action;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->status = 10;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param Users $user
     * @return $this
     */
    public function setUser(Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @param array $params
     * @return $this
     */
    public function setAction(string $action, $params = []): self
    {
        $this->action = vsprintf($action, $params);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @param DateTime $created_at
     * @return $this
     */
    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
