<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixture extends Fixture implements DependentFixtureInterface
{
    public const POST_FIRST_REFERENCE = 'post-first';

    public function load(ObjectManager $manager): void
    {
        $items = [
            [
                'title' => 'Title of first post!',
                'description' => '<p>Description of first post. Lorem ipsum dolor sit amet, fusce ullamcorper montes ipsum lorem, suscipit augue convallis eu sodales tincidunt, proin maecenas risus ultricies luctus. Etiam velit. Maecenas id sodales. Ante mi neque.</p>',
                'content' => '<p>Content for first post. Congue tincidunt pede mi sed. Eget per rutrum massa, vitae ut cursus consequat vestibulum, adipiscing non aliquet accumsan. Morbi fringilla volutpat vel magna, mauris ut sem pellentesque libero, neque congue, condimentum pellentesque purus phasellus augue, et platea pharetra sit dolor nibh quam. Dolor quam. Praesent mauris magnam cras lorem interdum, lorem curabitur tempor at perferendis, luctus volutpat. Amet sodales elit eu ipsum maecenas duis, ultrices consectetuer et vestibulum suspendisse congue sem, nisl in mollis nec maecenas pulvinar congue. Diam hendrerit augue pellentesque elit, rhoncus porttitor vestibulum nibh, dapibus aliquam, dictum neque suscipit risus, scelerisque ut fermentum. Quis id hendrerit, natoque eget nam donec pede sit eros. Metus at, mauris non, nulla in consectetuer, sed ipsum, eget sed. Adipiscing mollis nostra pulvinar. Et nunc nibh ultricies, dolorem pellentesque faucibus molestie proin at enim, cras leo nulla, iaculis natoque aliquet sodales est mattis. Faucibus est, sed semper velit sit ac, eu dui cursus, eu suscipit vestibulum platea imperdiet orci, quam donec commodo lorem sed iaculis accumsan.</p><p>Molestie porta amet, ultrices aliquet porttitor hac feugiat. Neque ante sed condimentum sit, sapien tortor. Mollit scelerisque nam praesent ea, porta id ridiculus taciti elit volutpat eu, vel auctor. Et nisl nunc, aliquam justo posuere, ut in, turpis justo elit. At dui vehicula eu in, per aliquam, rhoncus tempor et maecenas nec in tortor.</p><p>Vivamus ac. Ac augue. Purus nisl tellus. Sem etiam elementum, quis felis aliquam sodales, ornare quam morbi proin ut dui. Lectus in quis duis, vehicula quam nec at ac ut venenatis, velit vestibulum amet vel ut placerat pharetra, facilisi aenean vitae. Quisque mollis etiam elit et ullamcorper tempus, vulputate eros tincidunt at sem. Nulla imperdiet, ut sit et curabitur. Integer wisi malesuada nec metus lorem. Sapien ac aenean.</p>',
                'category' => $this->getReference(CategoryFixture::CATEGORY_PARENT_REFERENCE),
                'user' => $this->getReference(UserFixture::ADMIN_USER_REFERENCE),
                'reference' => self::POST_FIRST_REFERENCE,
            ],
            [
                'title' => 'Title of second post!',
                'description' => '<p>Description of second post. Lorem ipsum dolor sit amet, fusce ullamcorper montes ipsum lorem, suscipit augue convallis eu sodales tincidunt, proin maecenas risus ultricies luctus. Etiam velit. Maecenas id sodales. Ante mi neque.</p>',
                'content' => '<p>Content for second post. Congue tincidunt pede mi sed. Eget per rutrum massa, vitae ut cursus consequat vestibulum, adipiscing non aliquet accumsan. Morbi fringilla volutpat vel magna, mauris ut sem pellentesque libero, neque congue, condimentum pellentesque purus phasellus augue, et platea pharetra sit dolor nibh quam. Dolor quam. Praesent mauris magnam cras lorem interdum, lorem curabitur tempor at perferendis, luctus volutpat. Amet sodales elit eu ipsum maecenas duis, ultrices consectetuer et vestibulum suspendisse congue sem, nisl in mollis nec maecenas pulvinar congue. Diam hendrerit augue pellentesque elit, rhoncus porttitor vestibulum nibh, dapibus aliquam, dictum neque suscipit risus, scelerisque ut fermentum. Quis id hendrerit, natoque eget nam donec pede sit eros. Metus at, mauris non, nulla in consectetuer, sed ipsum, eget sed. Adipiscing mollis nostra pulvinar. Et nunc nibh ultricies, dolorem pellentesque faucibus molestie proin at enim, cras leo nulla, iaculis natoque aliquet sodales est mattis. Faucibus est, sed semper velit sit ac, eu dui cursus, eu suscipit vestibulum platea imperdiet orci, quam donec commodo lorem sed iaculis accumsan.</p><p>Molestie porta amet, ultrices aliquet porttitor hac feugiat. Neque ante sed condimentum sit, sapien tortor. Mollit scelerisque nam praesent ea, porta id ridiculus taciti elit volutpat eu, vel auctor. Et nisl nunc, aliquam justo posuere, ut in, turpis justo elit. At dui vehicula eu in, per aliquam, rhoncus tempor et maecenas nec in tortor.</p><p>Vivamus ac. Ac augue. Purus nisl tellus. Sem etiam elementum, quis felis aliquam sodales, ornare quam morbi proin ut dui. Lectus in quis duis, vehicula quam nec at ac ut venenatis, velit vestibulum amet vel ut placerat pharetra, facilisi aenean vitae. Quisque mollis etiam elit et ullamcorper tempus, vulputate eros tincidunt at sem. Nulla imperdiet, ut sit et curabitur. Integer wisi malesuada nec metus lorem. Sapien ac aenean.</p>',
                'category' => $this->getReference(CategoryFixture::CATEGORY_CHILD_REFERENCE),
                'user' => $this->getReference(UserFixture::USER_USER_REFERENCE),
            ],
            [
                'title' => 'Title of third post!',
                'description' => '<p>Description of third post. Lorem ipsum dolor sit amet, fusce ullamcorper montes ipsum lorem, suscipit augue convallis eu sodales tincidunt, proin maecenas risus ultricies luctus. Etiam velit. Maecenas id sodales. Ante mi neque.</p>',
                'content' => '<p>Content for third post. Congue tincidunt pede mi sed. Eget per rutrum massa, vitae ut cursus consequat vestibulum, adipiscing non aliquet accumsan. Morbi fringilla volutpat vel magna, mauris ut sem pellentesque libero, neque congue, condimentum pellentesque purus phasellus augue, et platea pharetra sit dolor nibh quam. Dolor quam. Praesent mauris magnam cras lorem interdum, lorem curabitur tempor at perferendis, luctus volutpat. Amet sodales elit eu ipsum maecenas duis, ultrices consectetuer et vestibulum suspendisse congue sem, nisl in mollis nec maecenas pulvinar congue. Diam hendrerit augue pellentesque elit, rhoncus porttitor vestibulum nibh, dapibus aliquam, dictum neque suscipit risus, scelerisque ut fermentum. Quis id hendrerit, natoque eget nam donec pede sit eros. Metus at, mauris non, nulla in consectetuer, sed ipsum, eget sed. Adipiscing mollis nostra pulvinar. Et nunc nibh ultricies, dolorem pellentesque faucibus molestie proin at enim, cras leo nulla, iaculis natoque aliquet sodales est mattis. Faucibus est, sed semper velit sit ac, eu dui cursus, eu suscipit vestibulum platea imperdiet orci, quam donec commodo lorem sed iaculis accumsan.</p><p>Molestie porta amet, ultrices aliquet porttitor hac feugiat. Neque ante sed condimentum sit, sapien tortor. Mollit scelerisque nam praesent ea, porta id ridiculus taciti elit volutpat eu, vel auctor. Et nisl nunc, aliquam justo posuere, ut in, turpis justo elit. At dui vehicula eu in, per aliquam, rhoncus tempor et maecenas nec in tortor.</p><p>Vivamus ac. Ac augue. Purus nisl tellus. Sem etiam elementum, quis felis aliquam sodales, ornare quam morbi proin ut dui. Lectus in quis duis, vehicula quam nec at ac ut venenatis, velit vestibulum amet vel ut placerat pharetra, facilisi aenean vitae. Quisque mollis etiam elit et ullamcorper tempus, vulputate eros tincidunt at sem. Nulla imperdiet, ut sit et curabitur. Integer wisi malesuada nec metus lorem. Sapien ac aenean.</p>',
                'category' => $this->getReference(CategoryFixture::CATEGORY_SUBCHILD_REFERENCE),
                'user' => $this->getReference(UserFixture::USER_USER_REFERENCE),
            ]
        ];

        for ($i = 0; $i < 30; ++$i) {
            $items[] = [
                'title' => 'Pagination test!',
                'description' => '<p>Description of third post. Lorem ipsum dolor sit amet, fusce ullamcorper montes ipsum lorem, suscipit augue convallis eu sodales tincidunt, proin maecenas risus ultricies luctus. Etiam velit. Maecenas id sodales. Ante mi neque.</p>',
                'content' => '<p>Content for third post. Congue tincidunt pede mi sed. Eget per rutrum massa, vitae ut cursus consequat vestibulum, adipiscing non aliquet accumsan. Morbi fringilla volutpat vel magna, mauris ut sem pellentesque libero, neque congue, condimentum pellentesque purus phasellus augue, et platea pharetra sit dolor nibh quam. Dolor quam. Praesent mauris magnam cras lorem interdum, lorem curabitur tempor at perferendis, luctus volutpat. Amet sodales elit eu ipsum maecenas duis, ultrices consectetuer et vestibulum suspendisse congue sem, nisl in mollis nec maecenas pulvinar congue. Diam hendrerit augue pellentesque elit, rhoncus porttitor vestibulum nibh, dapibus aliquam, dictum neque suscipit risus, scelerisque ut fermentum. Quis id hendrerit, natoque eget nam donec pede sit eros. Metus at, mauris non, nulla in consectetuer, sed ipsum, eget sed. Adipiscing mollis nostra pulvinar. Et nunc nibh ultricies, dolorem pellentesque faucibus molestie proin at enim, cras leo nulla, iaculis natoque aliquet sodales est mattis. Faucibus est, sed semper velit sit ac, eu dui cursus, eu suscipit vestibulum platea imperdiet orci, quam donec commodo lorem sed iaculis accumsan.</p><p>Molestie porta amet, ultrices aliquet porttitor hac feugiat. Neque ante sed condimentum sit, sapien tortor. Mollit scelerisque nam praesent ea, porta id ridiculus taciti elit volutpat eu, vel auctor. Et nisl nunc, aliquam justo posuere, ut in, turpis justo elit. At dui vehicula eu in, per aliquam, rhoncus tempor et maecenas nec in tortor.</p><p>Vivamus ac. Ac augue. Purus nisl tellus. Sem etiam elementum, quis felis aliquam sodales, ornare quam morbi proin ut dui. Lectus in quis duis, vehicula quam nec at ac ut venenatis, velit vestibulum amet vel ut placerat pharetra, facilisi aenean vitae. Quisque mollis etiam elit et ullamcorper tempus, vulputate eros tincidunt at sem. Nulla imperdiet, ut sit et curabitur. Integer wisi malesuada nec metus lorem. Sapien ac aenean.</p>',
                'category' => $this->getReference(CategoryFixture::CATEGORY_OTHER_REFERENCE),
                'user' => $this->getReference(UserFixture::USER_USER_REFERENCE),
            ];
        }

        for ($i = 0; $i < count($items); $i++) {
            $post = new Post();
            $post->setTitle($items[$i]['title']);
            $post->setDescription($items[$i]['description']);
            $post->setContent($items[$i]['content']);
            $post->setCategory($items[$i]['category']);
            $post->setUser($items[$i]['user']);
            if (key_exists('reference', $items[$i])) {
                $this->setReference($items[$i]['reference'], $post);
            }

            $manager->persist($post);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
            UserFixture::class,
        ];
    }
}
