<?php

namespace App\Repository;

use App\Entity\UsersLogs;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UsersLogsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UsersLogs::class);
    }

    public function findLatest(int $user_id, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('ul')
            ->orderBy('ul.created_at', 'DESC')
            ->where('ul.status=10 AND ul.user=:userId')
            ->setParameter('userId', $user_id);

        return (new Paginator($qb, 100))->paginate($page);
    }
}
