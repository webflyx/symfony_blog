<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfoType;
use App\Entity\UserProfile;
use App\Form\UserDeleteType;
use App\Form\UserImageType;
use App\Form\UserPasswordType;
use App\Services\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('dashboard/dashboard.html.twig', []);
    }

    #[Route('/dashboard/profile/{id}', name: 'app_profile')]
    public function profile(EntityManagerInterface $entityManager, Request $request, ImageUploader $imageUploader, User $user, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        // User Image
        $formUserImage = $this->createForm(UserImageType::class);
        $formUserImage->handleRequest($request);

        if ($formUserImage->isSubmitted() && $formUserImage->isValid()) {
            $profileImageFile = $formUserImage->get('profileImage')->getData();

            if ($profileImageFile) {
                $newFileName = $imageUploader->upload($profileImageFile);

                $profile = $user->getUserProfile() ?? new UserProfile();
                $profile->setImage($newFileName);
                $user->setUserProfile($profile);

                $entityManager->persist($profile);
                $entityManager->flush();
            }

            //Flash messages
            $this->addFlash('profile-updated', 'Profile image has been updated!');

            //Redirect after sent
            return $this->redirect($request->headers->get('referer'));
        }

        // User Info
        $formUserInfo = $this->createForm(UserInfoType::class);
        $formUserInfo->handleRequest($request);
        $currName = $user->getUserProfile()?->getName() ?? '';
        $currEmail = $user->getEmail();

        if ($formUserInfo->isSubmitted() && $formUserInfo->isValid()) {
            $profile = $user->getUserProfile() ?? new UserProfile();
            if ($formUserInfo->get('name')->getData()) {
                $profile->setName($formUserInfo->get('name')->getData());
            }
            if ($formUserInfo->get('email')->getData()) {
                $user->setEmail($formUserInfo->get('email')->getData());
            }
            $user->setUserProfile($profile);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('profile-updated', 'Profile information has been updated!');

            return $this->redirect($request->headers->get('referer'));
        }

        // User Password
        $formUserPassword = $this->createForm(UserPasswordType::class);
        $formUserPassword->handleRequest($request);

        if ($formUserPassword->isSubmitted() && $formUserPassword->isValid()) {

            $newPassword = $userPasswordHasher->hashPassword(
                $user,
                $formUserPassword->get('plainPassword')->getData()
            );

            $user->setPassword($newPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('profile-updated', 'Profile password has been updated!');

            return $this->redirect($request->headers->get('referer'));
        }

        // User Delete
        $formUserDelete = $this->createForm(UserDeleteType::class);
        $formUserDelete->handleRequest($request);
        
        if ($formUserDelete->isSubmitted() && $formUserDelete->isValid()) {

            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your Account has been deleted.');

            $request->getSession()->invalidate();

            return $this->redirectToRoute('posts.index');
        }

        return $this->render('dashboard/profile.html.twig', [
            'formUserImage' => $formUserImage,
            'formUserInfo' => $formUserInfo,
            'currName' => $currName,
            'currEmail' => $currEmail,
            'formUserPassword' => $formUserPassword,
            'formUserDelete' => $formUserDelete,
        ]);
    }
}
