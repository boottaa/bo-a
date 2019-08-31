<?php

namespace App\Repository;

use App\Entity\Followers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Followers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Followers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Followers[]    findAll()
 * @method Followers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Followers::class);
    }

    // /**
    //  * @return Followers[] Returns an array of Followers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Followers
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
