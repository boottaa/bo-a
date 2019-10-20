<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\Users;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use Symfony\Bridge\Doctrine\RegistryInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    /**
     * @param string $entityClass The class name of the entity this repository manages
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @param array $tags
     * @param int $page
     * @return Paginator
     * @throws Exception
     */
    public function findLatest(array $tags, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('n')
            ->addSelect('a', 't')
            ->innerJoin('n.author', 'a')
            ->innerJoin('n.tags', 't')
            ->orderBy('n.created_at', 'DESC')
            ->where('n.published_at <= :now')
            ->andWhere('n.status = :status')
            ->setParameters(['now' => new \DateTime(), 'status' => News::STATUS_IS_PUBLISH]);
        if(!empty($tags) && current($tags) !== 'all'){
            $qb->andWhere($qb->expr()->andX(
                $qb->expr()->in('t.name', ':tag')
            ))->setParameter('tag', implode(',', $tags));
        }

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @param Users $user
     * @param int $page
     * @return Paginator
     * @throws Exception
     */
    public function findLatestAuthor(Users $user, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('n')
            ->orderBy('n.created_at', 'DESC')
            ->where('n.published_at <= :now AND n.status= :status AND n.author = :author')
            ->setParameters([
                'now'    => new \DateTime(),
                'author' => $user->getId(),
                'status' => News::STATUS_IS_PUBLISH
            ]);

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @param int $page
     * @param Users $user
     * @return Paginator
     * @throws Exception
     */
    public function findLatestForAdmin(int $page = 1, Users $user): Paginator
    {
        $qb = $this->createQueryBuilder('n')
            ->orderBy('n.created_at', 'DESC');

        if (!in_array('ROLE_MODERATOR', $user->getRoles())) {
            $qb->where('n.author = :author')
                ->setParameter('author', $user);
        }

        return (new Paginator($qb))->paginate($page);
    }

}
