<?php

namespace App\Twig\Components;

use App\Repository\PostsRepository;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('search')]
final class SearchComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    private $posts;

    public function __construct(PostsRepository $posts)
    {
        $this->posts = $posts;
    }

    public function getPosts(): array
    {
        if (strlen($this->query) > 1) {
            return $this->query ? $this->posts->searchQuery($this->query) : [];
        } else {
            return [];
        }
    }
}
