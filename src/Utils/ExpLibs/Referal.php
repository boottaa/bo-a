<?php

declare(strict_types=1);

namespace App\Utils\ExpLibs;

use App\Entity\Users;
use App\Entity\UsersLogs;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Class Like
 */
class Referal
{
    public const ADD_EXPA_GET_REFERAL = 500; //Получил реферала
    public const ADD_EXPA_REFERAL = 200; //Зарегестрировался по реферальной ссыдке

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
     * @param Users $userGetReferal
     * @param Users $userReferal
     */
    public function add(Users $userGetReferal, Users $userReferal)
    {
        $userGetReferal->addExpa(self::ADD_EXPA_GET_REFERAL);
        $userReferal->addExpa(self::ADD_EXPA_REFERAL);

        $userLogs = new UsersLogs();
        $userLogs->setUser($userGetReferal);
        $userLogs->setAction(UsersLogs::T_GET_NEW_REFERAL, [$userReferal->getLName() . ' ' . $userReferal->getFName(), self::ADD_EXPA_GET_REFERAL]);
        $this->entityManager->persist($userLogs);

        $userLogs = new UsersLogs();
        $userLogs->setUser($userReferal);
        $userLogs->setAction(UsersLogs::T_NEW_REFERAL, [$userGetReferal->getLName() . ' ' . $userGetReferal->getFName(), self::ADD_EXPA_REFERAL]);
        $this->entityManager->persist($userLogs);
    }

}