<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Title is short, 5 characters is the minimum',
        maxMessage: 'Title is long, 255 characters is the maximum'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 50,
        max: 1000,
        minMessage: 'Content is short, 50 characters is the minimum',
        maxMessage: 'Content is long, 1000 characters is the maximum'
    )]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likedPosts')]
    #[ORM\JoinTable(name: 'likes')]
    private Collection $usersLiked;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'dislikedPosts')]
    #[ORM\JoinTable(name: 'dislikes')]
    private Collection $usersDisliked;

    public function __construct()
    {
        $this->usersLiked = new ArrayCollection();
        $this->usersDisliked = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersLiked(): Collection
    {
        return $this->usersLiked;
    }

    public function addUsersLiked(User $usersLiked): static
    {
        if (!$this->usersLiked->contains($usersLiked)) {
            $this->usersLiked->add($usersLiked);
            $usersLiked->addLikedPost($this);
        }

        return $this;
    }

    public function removeUsersLiked(User $usersLiked): static
    {
        if ($this->usersLiked->removeElement($usersLiked)) {
            $usersLiked->removeLikedPost($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersDisliked(): Collection
    {
        return $this->usersDisliked;
    }

    public function addUsersDisliked(User $usersDisliked): static
    {
        if (!$this->usersDisliked->contains($usersDisliked)) {
            $this->usersDisliked->add($usersDisliked);
            $usersDisliked->addDislikedPost($this);
        }

        return $this;
    }

    public function removeUsersDisliked(User $usersDisliked): static
    {
        if ($this->usersDisliked->removeElement($usersDisliked)) {
            $usersDisliked->removeDislikedPost($this);
        }

        return $this;
    }
}
