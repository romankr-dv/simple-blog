<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/17/19
 * Time: 11:49 AM.
 */

namespace App\Menu;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private $factory;
    private $em;
    private $requestStack;
    private $token;
    private $security;

    public function __construct(FactoryInterface $factory, EntityManagerInterface $em, RequestStack $requestStack, TokenStorageInterface $tokenStorage, Security $security)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->token = $tokenStorage->getToken();
        $this->security = $security;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', $options['root_class']);

        // add 'Home' link
        $menu
            ->addChild('Home', ['route' => 'post_index'])
            ->setLinkAttribute('class', $options['link_class'])
        ;

        // add 'Last post' link
        /** @var PostRepository $postRepository */
        $postRepository = $this->em->getRepository(Post::class);
        $recentPost = $postRepository->findMostRecent();

        if ($recentPost !== null) {
            $menu
                ->addChild('Most Recent', [
                    'route' => 'post_show',
                    'routeParameters' => ['slug' => $recentPost->getSlug()],
                ])
                ->setLinkAttribute('class', $options['link_class'])
            ;
        }

        if ($this->security->isGranted('ROLE_USER')) {
            // add 'Categories' link
            $menu
                ->addChild('Categories', ['route' => 'category_index'])
                ->setLinkAttribute('class', $options['link_class'])
            ;
        }

        return $menu;
    }

    public function createAuthMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', $options['root_class']);

        // add 'Login' link
        $menu
            ->addChild('Log in', ['route' => 'app_login'])
            ->setLinkAttribute('class', $options['link_class'])
        ;

        // add 'Register' link
        $menu
            ->addChild('Register', ['route' => 'app_register'])
            ->setLinkAttribute('class', $options['link_class'])
        ;

        return $menu;
    }

    public function createUserMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', $options['root_class']);

        // add 'Admin panel' link
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $menu
                ->addChild('Admin Panel', ['route' => 'sonata_admin_dashboard'])
                ->setLinkAttribute('class', $options['link_class'])
            ;
        }

        // add 'Create post' link
        $menu
            ->addChild('New Post', ['route' => 'post_new'])
            ->setLinkAttribute('class', $options['link_class'])
        ;

        /** @var User $user */
        $user = $this->token->getUser();

        $menu
            ->addChild($user->getUsername(), ['route' => 'home'])
            ->setLinkAttribute('class', $options['link_class'])
            ->setExtra('translation_domain', false)
        ;

        // add 'Logout' link
        $menu
            ->addChild('Log out', ['route' => 'app_logout'])
            ->setLinkAttribute('class', $options['link_class'])
        ;

        return $menu;
    }

    public function createCategoryMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', $options['root_class']);

        /** @var CategoryRepository $repository */
        $repository = $this->em->getRepository(Category::class);

        /** @var Category[] $categories */
        $categories = $repository->findBy(['parent' => null], ['order' => 'ASC']);

        $query = $this->requestStack->getCurrentRequest()->query->all();

        $generate = function ($categories, $menu) use ($query, &$generate) {
            /** @var Category[] $categories */
            foreach ($categories as $category) {
                $menu->addChild($category->getTitle(), [
                    'route' => 'post_index',
                    'routeParameters' => array_merge($query, ['filter_post[category]' => $category->getId()]),
                ]);

                if ($category->getChildren()->count() !== 0) {
                    $generate($category->getChildren(), $menu[$category->getTitle()]);
                }
            }
        };

        $generate($categories, $menu);

        return $menu;
    }
}
