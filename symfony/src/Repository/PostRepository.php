<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/15/19
 * Time: 11:36 AM.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\FilterPost;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('post')
            ->orderBy('post.createdAt', 'DESC')
            ->join('post.category', 'category')
        ;
    }

    private function filterCategoryQueryBuilder($categories, QueryBuilder $queryBuilder): QueryBuilder
    {
        /** @var Category $category */
        foreach ($categories as $category) {
            $queryBuilder
                ->orWhere('post.category = :category'.$category->getId())
                ->setParameter('category'.$category->getId(), $category)
            ;

            if ($category->getChildren()->count() !== 0) {
                $queryBuilder = $this->filterCategoryQueryBuilder($category->getChildren(), $queryBuilder);
            }
        }

        return $queryBuilder;
    }

    public function filterQueryBuilder(FilterPost $filter, QueryBuilder $queryBuilder = null): QueryBuilder
    {
        if ($queryBuilder === null) {
            $queryBuilder = $this->createQueryBuilder('post');
        }

        // filter by category

        if ($filter->getCategory() !== null) {
            $queryBuilder = $this->filterCategoryQueryBuilder([$filter->getCategory()], $queryBuilder);
        }

        // filter by tag

        if ($filter->getTag() !== null) {
            $queryBuilder
                ->andWhere(':tag MEMBER OF post.tags')
                ->setParameter('tag', $filter->getTag())
            ;
        }

        // filter by createdAt

        $queryBuilder
            ->andWhere('( post.createdAt BETWEEN :from AND :to )
                OR ( :from IS NULL AND :to IS NULL )
                OR ( :from IS NULL AND post.createdAt <= :to )
                OR ( :to   IS NULL AND post.createdAt >= :from )')
            ->setParameter('from', $filter->getFrom())
            ->setParameter('to', $filter->getTo())
        ;

        return $queryBuilder;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return Post|null
     */
    public function findMostRecent(): ?Post
    {
        return $this
            ->findAllQueryBuilder()
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByTitleQueryBuilder($title)
    {
        return $this
            ->findAllQueryBuilder()
            ->where('post.title LIKE :title')
            ->setParameter('title', '%' . $title . '%')
        ;
    }

    public function findRecomendedQueryBuilder($categories, $limit): QueryBuilder
    {
        $queryBuilder = $this->findAllQueryBuilder();

        $queryBuilder = $this->filterCategoryQueryBuilder($categories, $queryBuilder);

        return $queryBuilder
            ->andWhere('post.createdAt >= :date')
            ->setParameter('date', date("Y-m-j H:i:s", strtotime("-1 days")))
            ->setMaxResults($limit)
        ;
    }
}
