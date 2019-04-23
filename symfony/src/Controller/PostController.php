<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/15/19
 * Time: 10:41 AM.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\FilterPost;
use App\Entity\Post;
use App\Entity\PostLikedByUser;
use App\Form\CommentType;
use App\Form\FilterPostType;
use App\Form\PostType;
use App\Repository\PostLikedByUserRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/posts")
 */
class PostController extends AbstractController
{
    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(Breadcrumbs $breadcrumbs, TranslatorInterface $translator)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->translator = $translator;
        $this->breadcrumbs->addRouteItem($this->translator->trans('Home'), 'post_index');
    }

    /**
     * @param Request $request
     * @param PostRepository $repository
     * @param PaginatorInterface $paginator
     * @return Response
     *
     * @Route("", name="post_index", methods={"GET"})
     */
    public function index(Request $request, PostRepository $repository, PaginatorInterface $paginator): Response
    {
        /// Breadcrumbs

        if (is_array($request->get('filter_post')) && array_key_exists('category', $request->get('filter_post'))) {
            $category = $this->getDoctrine()->getManager()->find(Category::class, $request->get('filter_post')['category']);

            if ($category instanceof Category) {
                $f = function (Category $category) use (&$f) {
                    if ($category->getParent() instanceof Category) {
                        $f($category->getParent());
                    }

                    $this->breadcrumbs->addRouteItem(
                        $category->getTitle(),
                        'post_index',
                        ['filter_post[category]' => $category->getId()]
                    );
                };

                $f($category);
            }
        }


        /// !Breadcrumbs

        switch ($request->query->get('view')) {
            case 'table':
                $template = 'post/index/table.html.twig';
                $count = Post::QUANTITY_PER_PAGE['table'];
                break;
            case 'list':
                $template = 'post/index/list.html.twig';
                $count = Post::QUANTITY_PER_PAGE['list'];
                break;
            default:
                $template = 'post/index/list.html.twig';
                $count = Post::QUANTITY_PER_PAGE['list'];
        }

        $filter = new FilterPost();
        $form = $this->createForm(FilterPostType::class, $filter);
        $form->handleRequest($request);

        if ($request->get('search') !== null) {
            $queryBuilder = $repository->findByTitleQueryBuilder($request->get('search'));
        } else {
            $queryBuilder = $repository->findAllQueryBuilder();
        }

        if (!$form->isEmpty()) {
            $queryBuilder = $repository->filterQueryBuilder($filter, $queryBuilder);
        }

        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            $request->query->getInt('post-count', $count)
        );

        return $this->render($template, [
            'pagination' => $pagination,
            'filter' => $filter,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/new", name="post_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {

        /// Breadcrumbs

        $this->breadcrumbs->addRouteItem(
            $this->translator->trans('New Post'),
            'post_new'
        );

        /// !Breadcrumbs

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post created');

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return Response
     *
     * @Route("/{slug}/edit", name="post_edit", methods={"GET", "POST"})
     * @IsGranted("POST_EDIT", subject="post")
     */
    public function edit(Request $request, Post $post): Response
    {

        /// Breadcrumbs

        if ($post->getCategory() instanceof Category) {
            $f = function (Category $category) use (&$f) {
                if ($category->getParent() instanceof Category) {
                    $f($category->getParent());
                }

                $this->breadcrumbs->addRouteItem(
                    $category->getTitle(),
                    'post_index',
                    ['filter_post[category]' => $category->getId()]
                );
            };

            $f($post->getCategory());
        }

        $this->breadcrumbs->addRouteItem(
            $post->getTitle(),
            'post_show',
            ['slug' => $post->getSlug()]
        );

        $this->breadcrumbs->addRouteItem(
            $this->translator->trans('Edit Post'),
            'post_edit',
            ['slug' => $post->getSlug()]
        );

        /// !Breadcrumbs

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Post updated');

            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Post $post
     * @return Response
     *
     * @Route("/{slug}", name="post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        /// Breadcrumbs

        if ($post->getCategory() instanceof Category) {
            $f = function (Category $category) use (&$f) {
                if ($category->getParent() instanceof Category) {
                    $f($category->getParent());
                }

                $this->breadcrumbs->addRouteItem(
                    $category->getTitle(),
                    'post_index',
                    ['filter_post[category]' => $category->getId()]
                );
            };

            $f($post->getCategory());
        }

        $this->breadcrumbs->addRouteItem(
            $post->getTitle(),
            'post_show',
            ['slug' => $post->getSlug()]
        );

        /// !Breadcrumbs

        $comment = new Comment();
        $comment->setPost($post);
        $commentForm = $this->createForm(CommentType::class, $comment);
        $comment->setPost(null);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comment_form' => $commentForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return Response
     *
     * @Route("/{slug}", name="post_delete", methods={"DELETE"})
     * @IsGranted("POST_EDIT", subject="post")
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();

            $this->addFlash('success', 'Post deleted');
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Post $post
     * @return Response
     *
     * @Route("/{slug}/like", name="post_like", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function like(EntityManagerInterface $entityManager, Post $post): Response
    {
        /** @var PostLikedByUserRepository $repository */
        $repository = $entityManager->getRepository(PostLikedByUser::class);

        if (!$repository->findOneByPostAndUser($post, $this->getUser())) {
            $like = new PostLikedByUser();
            $like->setUser($this->getUser());
            $like->setPost($post);

            $entityManager->persist($like);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Post $post
     * @return Response
     *
     * @Route("/{slug}/dislike", name="post_dislike", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function dislike(EntityManagerInterface $entityManager, Post $post): Response
    {
        $repository = $entityManager->getRepository(PostLikedByUser::class);

        /** @var PostLikedByUserRepository $repository */
        if ($like = $repository->findOneByPostAndUser($post, $this->getUser())) {
            $entityManager->remove($like);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
    }
}
