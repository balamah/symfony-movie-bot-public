<?php

namespace App\Entity;

use App\Repository\UserWaitedMovieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserWaitedMovieRepository::class)]
class UserWaitedMovie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userWaitedMovies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'userWaitedMovies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WaitedMovie $waited_movie_id = null;

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

    public function getWaitedMovieId(): ?WaitedMovie
    {
        return $this->waited_movie_id;
    }

    public function setWaitedMovieId(?WaitedMovie $waited_movie_id): static
    {
        $this->waited_movie_id = $waited_movie_id;

        return $this;
    }
}
