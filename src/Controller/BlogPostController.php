<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Form\BlogPostEntryType;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogPostController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function list(BlogPostRepository $blogPostRepository): Response
    {
        $posts = $blogPostRepository->findAll();
        return $this->render('blog/list.html.twig', [
            'posts' => $posts,
        ]);
    }

    
    #[Route('/blog/{id}', name: 'app_blog_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, BlogPostRepository $blogPostRepository): Response
    {
        $post = $blogPostRepository->find($id);
        return $this->render('blog/detail.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/blog/new', name: 'app_blog_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostEntryType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile) {
                $newFilename = uniqid().'.'.$mediaFile->guessExtension();
                $mediaFile->move(
                    $this->getParameter('media_directory'),
                    $newFilename
                );
                $blogPost->setMedia($newFilename);
            }

            $entityManager->persist($blogPost);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog');
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/blog/edit/{id}', name: 'app_blog_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, BlogPostRepository $blogPostRepository): Response
    {
        $post = $blogPostRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('L\'article n\'existe pas.');
        }

        $form = $this->createForm(BlogPostEntryType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_detail', ['id' => $post->getId()]);
        }

        return $this->render('blog/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/blog/delete/{id}', name: 'app_blog_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager, BlogPostRepository $blogPostRepository): Response
    {
        $post = $blogPostRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('L\'article n\'existe pas.');
        }

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog');
    }
}