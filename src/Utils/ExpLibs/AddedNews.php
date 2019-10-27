<?php

declare(strict_types=1);

namespace App\Utils\ExpLibs;

use App\Entity\News;
use App\Entity\Users;
use App\Entity\UsersLogs;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AddedNews
 */
class AddedNews
{
    public const ADD_EXPA = 200;
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
     * @param Users $user
     * @param News $news
     */
    public function add(Users $user, News $news): void
    {
        $user->addExpa(self::ADD_EXPA);

        $userLogs = new UsersLogs();
        $userLogs->setUser($user);
        $userLogs->setAction(UsersLogs::T_ADD_NEWS, [$news->getTitle(), self::ADD_EXPA]);
        $this->entityManager->persist($userLogs);
    }
}