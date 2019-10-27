<?php

declare(strict_types=1);

namespace App\Utils\ExpLibs;

use App\Entity\Users;
use App\Entity\UsersLogs;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Class Subscribe
 */
class Subscribe
{
    public const ADD_EXPA_GET_SUBSCRIBER = 100;
    public const ADD_EXPA_SUBSCRIBED = 10;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AddedNews constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Subscribe constructor.
     * @param Users $userGetSubscribe
     * @param Users $userSubscribed
     */
    public function add(Users $userGetSubscribe, Users $userSubscribed): void
    {
        $userGetSubscribe->addExpa(self::ADD_EXPA_GET_SUBSCRIBER);
        $userSubscribed->addExpa(self::ADD_EXPA_SUBSCRIBED);

        $userLogs = new UsersLogs();
        $userLogs->setUser($userGetSubscribe);
        $userLogs->setAction(UsersLogs::T_GET_NEW_SUBSCRIBE, [$userSubscribed->getLName() . ' ' . $userSubscribed->getFName(), self::ADD_EXPA_GET_SUBSCRIBER]);
        $this->entityManager->persist($userLogs);

        $userLogs = new UsersLogs();
        $userLogs->setUser($userSubscribed);
        $userLogs->setAction(UsersLogs::T_NEW_SUBSCRIBE, [$userGetSubscribe->getLName() . ' ' . $userGetSubscribe->getFName(), self::ADD_EXPA_SUBSCRIBED]);
        $this->entityManager->persist($userLogs);
    }

}