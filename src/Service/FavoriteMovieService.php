<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\UserFavoriteMovieRepository;
use App\Repository\FavoriteMovieRepository;

use App\Entity\UserFavoriteMovie;
use App\Entity\FavoriteMovie;

use App\Service\Interfaces\FavoriteMovieServiceInterface;
use App\Service\UserService;
use Exception;

class FavoriteMovieService implements FavoriteMovieServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FavoriteMovieRepository $favoriteMovieRepository,
        private readonly UserFavoriteMovieRepository $userFavoriteMovieRepository,
        private readonly UserService $userService
    ) {}

    public function getMovie(string $movieTitle): ?object
    {
        $movie = $this->favoriteMovieRepository->findBy(['name' => $movieTitle]);

        return (!$movie) ? (null) : $movie[0];
    }

    public function getUserFavoriteMovies(
        string $telegramId, bool $isMerged = true
    ): ?array
    {
        try {
            $userId = $this->userService->getUser($telegramId)->getId();
        } catch (\Throwable $e) {
            echo "$e\n";

            return null;
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('mf.name')
            ->from('App\Entity\FavoriteMovie', 'mf')
            ->leftJoin('App\Entity\UserFavoriteMovie', 'ufm',
                       'WITH', 'ufm.movie_favorite_id = mf.id')
            ->where('ufm.user_id = :user_id')
            ->setParameter('user_id', $userId);

        $queryResult = $queryBuilder->getQuery()->getResult();

        return (!$isMerged) ? $queryResult :
            $this->getMergedUserFavoriteMovies($queryResult);
    }

    public function isUserFavoriteMovie(string $telegramId, string $movieTitle): bool
    {
        $userFavoriteMovies = $this->getUserFavoriteMovies($telegramId);

        return (in_array($movieTitle, $userFavoriteMovies)) ? (true) : false;
    }
    
    public function addFavoriteMovie(string $telegramId, string $movieTitle): void
    {
        $favoriteMovie = new FavoriteMovie();
        $userFavoriteMovie = new UserFavoriteMovie();
        $user = $this->userService->getUser($telegramId);

        if (!$this->getMovie($movieTitle)) {
            $favoriteMovie->setName($movieTitle);

            $this->entityManager->persist($favoriteMovie);
            $this->entityManager->flush();
        }

        $userFavoriteMovie
            ->setUserId($user)
            ->setMovieFavoriteId($this->getMovie($movieTitle));

        $user->addUserFavoriteMovie($userFavoriteMovie);

        $this->entityManager->persist($userFavoriteMovie);
        $this->entityManager->flush();
    }

    public function removeFavoriteMovie(string $telegramId, string $movieTitle): void
    {
        if (!$this->getMovie($movieTitle)) return;

        $userId = $this->userService->getUser($telegramId)->getId();
        $movieId = $this->getMovie($movieTitle)->getId();

        $userFavoriteMovie = $this->userFavoriteMovieRepository->findOneBy(
            [
                'user_id' => $userId,
                'movie_favorite_id' => $movieId
            ]
        );

        $this->entityManager->remove($userFavoriteMovie);
        $this->entityManager->flush();
    }

    protected function getMergedUserFavoriteMovies(mixed $queryBuilderResult): ?array
    {
        $outputArray = [];
        foreach ($queryBuilderResult as $movie) {
            array_push($outputArray, $movie['name']);
        }

        return $outputArray;
    }
}
