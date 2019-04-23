<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comments")
 */
class CommentController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/new", name="comment_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment created');

            return $this->redirectToRoute('post_show', ['slug' => $comment->getPost()->getSlug()]);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/edit", name="comment_edit", methods={"GET", "POST"})
     * @IsGranted("COMMENT_EDIT", subject="comment")
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Comment updated');

            return $this->redirectToRoute('post_show', ['slug' => $comment->getPost()->getSlug()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     * @IsGranted("COMMENT_EDIT", subject="comment")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment deleted');
        }

        return $this->redirectToRoute('post_show', ['slug' => $comment->getPost()->getSlug()]);
    }
}
