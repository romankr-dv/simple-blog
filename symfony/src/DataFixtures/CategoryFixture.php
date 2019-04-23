<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/17/19
 * Time: 9:40 PM.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    public const CATEGORY_OTHER_REFERENCE = 'category-other';
    public const CATEGORY_CHILD_REFERENCE = 'category-child';
    public const CATEGORY_SUBCHILD_REFERENCE = 'category-subchild';
    public const CATEGORY_PARENT_REFERENCE = 'category-parent';

    public function load(ObjectManager $manager): void
    {
        $items = [
            [
                'title' => 'Symfony',
                'order' => 101000,
            ], [
                'title' => 'Other',
                'order' => 103000,
                'reference' => self::CATEGORY_OTHER_REFERENCE,
            ], [
                'title' => 'Parent',
                'order' => 102000,
                'reference' => self::CATEGORY_PARENT_REFERENCE,
                'children' => [
                    [
                        'title' => 'Child',
                        'order' => 101000,
                        'reference' => self::CATEGORY_CHILD_REFERENCE,
                        'children' => [
                            [
                                'title' => 'Second Child',
                                'order' => 101000,
                                'reference' => self::CATEGORY_SUBCHILD_REFERENCE,
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $f = function ($items, $parent = null) use (&$f, $manager) {
            foreach ($items as $item) {
                $category = new Category();
                $category->setTitle($item['title']);
                $category->setOrder($item['order']);

                if ($parent instanceof Category) {
                    $parent->addChild($category);
                }

                if (array_key_exists('children', $item)) {
                    $f($item['children'], $category);
                }

                if (array_key_exists('reference', $item)) {
                    $this->setReference($item['reference'], $category);
                }

                $manager->persist($category);
            }
        };

        $f($items);

        $manager->flush();
    }
}
