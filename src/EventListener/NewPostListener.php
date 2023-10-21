<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Posts;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class NewPostListener
{
    protected $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if(!$entity instanceof Posts){
            return;
        }    

        
        $entityManager = $args->getObjectManager();
        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user){
            $author = $entity->getAuthor()?->getUserProfile()?->getName() ? $entity->getAuthor()->getUserProfile()->getName() : $entity->getAuthor()->getEmail();
            
            $email = (new Email())
                ->from('hello@blog.com')
                ->to($user->getEmail())
                ->subject('New post from ' . $author)
                ->html('<p>See new post! ' . $entity->getTitle().'</p>');

            $this->mailer->send($email);
        }
    }
}