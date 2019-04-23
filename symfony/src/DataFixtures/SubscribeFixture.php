<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Subscribe;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SubscribeFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $subscribe = new Subscribe();

        /** @var Category $category */
        $category = $this->getReference(CategoryFixture::CATEGORY_CHILD_REFERENCE);
        $subscribe->setCategory($category);

        /** @var User $user */
        $user = $this->getReference(UserFixture::USER_USER_REFERENCE);
        $subscribe->setUser($user);

        $manager->persist($subscribe);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
            UserFixture::class,
        ];
    }
}
