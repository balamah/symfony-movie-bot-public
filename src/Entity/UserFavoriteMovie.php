<?php

namespace App\Entity;

use App\Repository\UserFavoriteMovieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserFavoriteMovieRepository::class)]
class UserFavoriteMovie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userFavoriteMovies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FavoriteMovie $movie_favorite_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getMovieFavoriteId(): ?FavoriteMovie
    {
        return $this->movie_favorite_id;
    }

    public function setMovieFavoriteId(?FavoriteMovie $movie_favorite_id): static
    {
        $this->movie_favorite_id = $movie_favorite_id;

        return $this;
    }
}
