<?php

namespace App\Twig\Components;

use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('like')]

final class LikeComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public Posts $post;

    public $isLiked;
    public $isDisliked;

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }


    #[LiveAction]
    public function like() {
        $this->post->addUsersLiked($this->security->getUser());
        $this->isLiked = true;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function undoLike() {
        $this->post->removeUsersLiked($this->security->getUser());
        $this->isLiked = false;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function dislike() {
        $this->post->addUsersDisliked($this->security->getUser());
        $this->isDisliked = true;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function undoDislike() {
        $this->post->removeUsersDisliked($this->security->getUser());
        $this->isDisliked = false;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

}
