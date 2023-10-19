<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiController extends AbstractController
{
    #[Route('/api/post/new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            
            $data = json_decode($request->getContent(), true);
            if(!$data || !$data['title'] || !$data['content']) {
                throw new \Exception("data not valid");
            }
            $post = new Posts;
            $post->setTitle($data['title']);
            $post->setContent($data['content']);
            $post->setAuthor($this->getUser());
            $post->setCreated(new \DateTime());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json([
                'message' => 'Post added!'
            ], 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Post not added!',
                'message' => $e->getMessage()
            ], 400);

        }
    }

    #[Route('/api/user/register', methods: ['POST'])]
    public function userRegister(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
    
        try {
            
            $data = json_decode($request->getContent(), true);
            if(!$data || !$data['email'] || !$data['password']) {
                throw new \Exception("data not valid");
            }
            $user = new User;
            $user->setEmail($data['email']);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $data['password']
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json([
                'message' => 'User registered!'
            ], 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'User not added!',
                'message' => $e->getMessage()
            ], 400);

        }
    }
}
