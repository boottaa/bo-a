<?php

namespace App\Repository;

use App\Entity\Likes;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LikesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    public function findLatest(int $user_id, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('l')
            ->addSelect('n')
            ->innerJoin('l.news', 'n')
            ->orderBy('n.created_at', 'DESC')
            ->where('n.status=1 AND l.user=:userId')
            ->setParameter('userId', $user_id);

        return (new Paginator($qb))->paginate($page);
    }

    /*
    public function findOneBySomeField($value): ?Tags
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
