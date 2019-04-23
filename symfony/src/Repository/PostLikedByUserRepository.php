<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostLikedByUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PostLikedByUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostLikedByUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostLikedByUser[]    findAll()
 * @method PostLikedByUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostLikedByUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PostLikedByUser::class);
    }

    public function findOneByPostAndUser(Post $post, User $user)
    {
        return $this->findOneBy([
            'post' => $post->getId(),
            'user' => $user->getId(),
        ]);
    }
}
