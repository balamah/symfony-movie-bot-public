<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, UserFavoriteMovie>
     */
    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: UserFavoriteMovie::class)]
    private Collection $userFavoriteMovies;

    #[ORM\Column(length: 255)]
    private ?string $telegram_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    public function __construct()
    {
        $this->userFavoriteMovies = new ArrayCollection();
        $this->userWaitedMovies = new ArrayCollection();
    }

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true,
                 options: ['default' => 'CURRENT_TIMESTAMP']
    )]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_banned = null;

    /**
     * @var Collection<int, UserWaitedMovie>
     */
    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: UserWaitedMovie::class, orphanRemoval: true)]
    private Collection $userWaitedMovies;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, UserFavoriteMovie>
     */
    public function getUserFavoriteMovies(): Collection
    {
        return $this->userFavoriteMovies;
    }

    public function addUserFavoriteMovie(UserFavoriteMovie $userFavoriteMovie): static
    {
        if (!$this->userFavoriteMovies->contains($userFavoriteMovie)) {
            $this->userFavoriteMovies->add($userFavoriteMovie);
            $userFavoriteMovie->setUserId($this);
        }

        return $this;
    }

    public function removeUserFavoriteMovie(UserFavoriteMovie $userFavoriteMovie): static
    {
        if ($this->userFavoriteMovies->removeElement($userFavoriteMovie)) {
            // set the owning side to null (unless already changed)
            if ($userFavoriteMovie->getUserId() === $this) {
                $userFavoriteMovie->setUserId(null);
            }
        }

        return $this;
    }

    public function getTelegramId(): ?string
    {
        return $this->telegram_id;
    }

    public function setTelegramId(string $telegram_id): static
    {
        $this->telegram_id = $telegram_id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->is_banned;
    }

    public function setBanned(?bool $is_banned): static
    {
        $this->is_banned = $is_banned;

        return $this;
    }

    /**
     * @return Collection<int, UserWaitedMovie>
     */
    public function getUserWaitedMovies(): Collection
    {
        return $this->userWaitedMovies;
    }

    public function addUserWaitedMovie(UserWaitedMovie $userWaitedMovie): static
    {
        if (!$this->userWaitedMovies->contains($userWaitedMovie)) {
            $this->userWaitedMovies->add($userWaitedMovie);
            $userWaitedMovie->setUserId($this);
        }

        return $this;
    }

    public function removeUserWaitedMovie(UserWaitedMovie $userWaitedMovie): static
    {
        if ($this->userWaitedMovies->removeElement($userWaitedMovie)) {
            // set the owning side to null (unless already changed)
            if ($userWaitedMovie->getUserId() === $this) {
                $userWaitedMovie->setUserId(null);
            }
        }

        return $this;
    }
}
