<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', methods:['GET'], name: 'posts.index')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig');
    }

    #[Route('/post/new', methods:['GET', 'POST'], name: 'posts.new', priority:2)]
    public function new(): Response
    {
        return $this->render('post/new.html.twig');
    }

    #[Route('/post/{id}/edit', methods:['GET', 'POST'], name: 'posts.edit', priority:2)]
    public function edit(): Response
    {
        //return $this->redirectToRoute('posts.index');
        return $this->render('post/edit.html.twig');
    }

    #[Route('/post/{id}/delete', methods:[']\'POST'], name: 'posts.delete', priority:2)]
    public function delete(): Response
    {
        //return $this->redirectToRoute('posts.index');
        return new Response('Post Deleted');
    }

    #[Route('/post/{id}', methods:['GET'], name: 'posts.single')]
    public function show($id): Response
    {
        return $this->render('post/single.html.twig', [
            'id' => $id
        ]);
    }

    #[Route('/posts/user/{id}', methods:['GET'], name: 'posts.user')]
    public function user($id): Response
    {
        return new Response(
            '<h1>TEST</h1>' . $id
        );
    }

    #[Route('/toggleFollow/{user}', methods:['GET'], name: 'toggleFollow')]
    public function toggleFollow($user): Response
    {
        return new Response(
            '<h1>TEST</h1>' . $user
        );
    }
}
