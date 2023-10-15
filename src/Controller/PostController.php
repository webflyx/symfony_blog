<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/', requirements:['_locale'=>'en|uk'])]
class PostController extends AbstractController
{
    #[Route('/{_locale}', methods:['GET'], name: 'posts.index')]
    public function index(Request $request, PostsRepository $posts, string $_locale = 'en'): Response
    {
        $postsAll = $posts->findAllPosts($request->query->getInt('page', 1));

        return $this->render('post/index.html.twig', [
            'posts' => $postsAll
        ]);
    }
    
    #[Route('{_locale}/post/{id}/', methods:['GET'], name: 'posts.single')]
    public function show(Posts $post): Response
    {
        return $this->render('post/single.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/post/new', methods:['GET', 'POST'], name: 'posts.new', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());
            $post->setCreated(new DateTime());
            $entityManager->persist($post);
            $entityManager->flush();


            //Flash messages
            $this->addFlash('success', 'Post Added!');

            //Redirect after sent
            return $this->redirectToRoute('posts.index');
        }    

        return $this->render('post/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/post/{id}/edit', methods:['GET', 'POST'], name: 'posts.edit', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(EntityManagerInterface $entityManager, Request $request, Posts $post): Response
    {

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            //$post->setAuthor($this->getUser());
            $post->setUpdated(new DateTime());
            $entityManager->persist($post);
            $entityManager->flush();


            //Flash messages
            $this->addFlash('success', 'Post Updated!');

            //Redirect after sent
            return $this->redirectToRoute('posts.index');
        }    

        return $this->render('post/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/post/{id}/delete', name: 'posts.delete', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(EntityManagerInterface $entityManager, Posts $post): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post Deleted!');
        return $this->redirectToRoute('posts.index');
    }

    #[Route('/posts/user/{id}', methods:['GET'], name: 'posts.user')]
    public function user($id, Request $request, PostsRepository $posts, UserRepository $users): Response
    {
        $postsAll = $posts->findAllPostsByUser($request->query->getInt('page', 1), $id);

        return $this->render('post/index.html.twig', [
            'posts' => $postsAll,
            'author' => $users->findBy(['id' => $id])[0]
        ]);
    }

    #[Route('/toggleFollow/{user}', methods:['GET'], name: 'toggleFollow')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleFollow($user): Response
    {
        return new Response(
            '<h1>TEST</h1>' . $user
        );
    }
}
