<?php
/**
 * Created by PhpStorm.
 * User: yamad
 * Date: 2/13/2019
 * Time: 10:38 AM.
 */

namespace App\Controller\Api;

use App\Entity\FilterPost;
use App\Entity\Post;
use App\Form\FilterPostType;
use App\Form\PostType;
use App\Repository\PostRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Prefix("/api")
 * @Rest\RouteResource("Posts")
 */
class PostController extends AbstractFOSRestController implements ClassResourceInterface
{
    private $paginator;
    private $repository;

    public function __construct(PaginatorInterface $paginator, PostRepository $repository)
    {
        $this->paginator = $paginator;
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the array of an posts",
     *     @SWG\Schema(
     *         type="object",
     *         required={"data", "pagination"},
     *         @SWG\Property(
     *             property="data",
     *             type="array",
     *             @SWG\Items(
     *                 ref=@Model(type=Post::class, groups={"api", "post"})
     *             )
     *         ),
     *         @SWG\Property(
     *             property="pagination",
     *             type="object",
     *             @SWG\Property(property="last", type="integer"),
     *             @SWG\Property(property="current", type="integer"),
     *             @SWG\Property(property="numItemsPerPage", type="integer"),
     *             @SWG\Property(property="first", type="integer"),
     *             @SWG\Property(property="pageCount", type="integer"),
     *             @SWG\Property(property="totalCount", type="integer"),
     *             @SWG\Property(property="pageRange", type="integer"),
     *             @SWG\Property(property="startPage", type="integer"),
     *             @SWG\Property(property="endPage", type="integer"),
     *             @SWG\Property(property="next", type="integer"),
     *             @SWG\Property(property="pagesInRange", type="array", @SWG\Items(type="integer")),
     *             @SWG\Property(property="firstPageInRange", type="integer"),
     *             @SWG\Property(property="lastPageInRange", type="integer"),
     *             @SWG\Property(property="currentItemCount", type="integer"),
     *             @SWG\Property(property="firstItemNumber", type="integer"),
     *             @SWG\Property(property="lastItemNumber", type="integer")
     *         ),
     *     )
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Page number"
     * )
     * @SWG\Parameter(
     *     name="count",
     *     in="query",
     *     type="integer",
     *     description="Count of posts per page"
     * )
     * @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     type="string",
     *     description="Value of title which is being searched"
     * )
     * @SWG\Parameter(
     *     name="filter_post[category]",
     *     in="query",
     *     type="integer",
     *     description="Id category that will be filtered"
     * )
     * @SWG\Parameter(
     *     name="filter_post[from]",
     *     in="query",
     *     type="string",
     *     format="yyyy-mm-dd",
     *     description="Date value 'from' that will be filtered"
     * )
     * @SWG\Parameter(
     *     name="filter_post[to]",
     *     in="query",
     *     type="string",
     *     format="yyyy-mm-dd",
     *     description="Date value 'to' that will be filtered"
     * )
     * @SWG\Tag(name="Posts")
     */
    public function getAction(Request $request)
    {
        $filter = new FilterPost();
        $form = $this->createForm(FilterPostType::class, $filter);
        $form->handleRequest($request);

        if ($request->get('search') !== null) {
            $queryBuilder = $this->repository->findByTitleQueryBuilder($request->get('search'));
        } else {
            $queryBuilder = $this->repository->findAllQueryBuilder();
        }

        if (!$form->isEmpty()) {
            $queryBuilder = $this->repository->filterQueryBuilder($filter, $queryBuilder);
        }

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            $request->query->getInt('count', Post::QUANTITY_PER_PAGE['api'])
        );

        $view = $this->view([
            'data' => $pagination,
            'pagination' => $pagination->getPaginationData(),
        ]);

        $view->getContext()->setGroups(['api', 'post']);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Response(
     *     response="201",
     *     description="Post is successfully created!",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="message",
     *             type="string"
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response="400",
     *     description="Post cannot be created!",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="message",
     *             type="string"
     *         ),
     *         @SWG\Property(
     *             property="errors",
     *             type="object"
     *         )
     *     )
     * )
     * @SWG\Parameter(
     *     name="post[title]",
     *     in="formData",
     *     type="string",
     *     description="Title of the post"
     * )
     * @SWG\Parameter(
     *     name="post[description]",
     *     in="formData",
     *     type="string",
     *     description="Description of the post"
     * )
     * @SWG\Parameter(
     *     name="post[content]",
     *     in="formData",
     *     type="string",
     *     description="Content of the post"
     * )
     * @SWG\Parameter(
     *     name="post[category]",
     *     in="formData",
     *     type="integer",
     *     description="Id category of the post"
     * )
     * @SWG\Tag(name="Posts")
     * @IsGranted("ROLE_USER")
     */
    public function postAction(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post, ['csrf_protection' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush();

            $view = $this->view([
                'message' => 'Post is successfully created!',
            ], Response::HTTP_CREATED);
        } else {
            $view = $this->view([
                'message' => 'Post cannot be created!',
                'errors' => $form->getErrors(false, true),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
