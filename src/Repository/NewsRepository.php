<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\Users;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @return News[] Returns an array of News objects
     */
    public function findLatest(int $page = 1): Paginator
    {

        $qb = $this->createQueryBuilder('n')
            ->addSelect('a')
            ->innerJoin('n.author', 'a')
            ->orderBy('n.created_at', 'DESC')
            ->where('n.published_at <= :now AND n.status = 1')
            ->setParameter('now', new \DateTime());

        return (new Paginator($qb))->paginate($page);
    }

    public function findLatestAuthor(int $page = 1, Users $user): Paginator
    {
        $qb = $this->createQueryBuilder('n')
            ->orderBy('n.created_at', 'DESC')
            ->where('n.published_at <= :now AND n.status = 1 AND n.author = :author')
            ->setParameters(['now' => new \DateTime(), 'author' => $user->getId()])
        ;

        return (new Paginator($qb))->paginate($page);
    }

    /*
    public function findOneBySomeField($value): ?News
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
