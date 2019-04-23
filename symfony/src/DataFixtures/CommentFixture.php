<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $items = [
            [
                'content' => 'First user comment!',
                'user' => $this->getReference(UserFixture::USER_USER_REFERENCE),
                'post' => $this->getReference(PostFixture::POST_FIRST_REFERENCE),
            ],
            [
                'content' => 'Second admin comment!',
                'user' => $this->getReference(UserFixture::ADMIN_USER_REFERENCE),
                'post' => $this->getReference(PostFixture::POST_FIRST_REFERENCE),
            ],
        ];

        for ($i = 0; $i < count($items); $i++) {
            $comment = new Comment();
            $comment->setContent($items[$i]['content']);
            $comment->setUser($items[$i]['user']);
            $comment->setPost($items[$i]['post']);

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PostFixture::class,
            UserFixture::class,
        ];
    }
}
