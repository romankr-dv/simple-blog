<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tag = new Tag();
        $tag->setTitle('Some tag');

        /** @var Post $post */
        $post = $this->getReference(PostFixture::POST_FIRST_REFERENCE);
        $tag->addPost($post);

        $manager->persist($tag);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PostFixture::class,
        ];
    }
}
