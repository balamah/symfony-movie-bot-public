<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\UserWaitedMovieRepository;
use App\Repository\WaitedMovieRepository;

use App\Entity\UserWaitedMovie;
use App\Entity\WaitedMovie;

use App\Service\Interfaces\WaitedMovieServiceInterface;
use App\Service\UserService;

class WaitedMovieService implements WaitedMovieServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WaitedMovieRepository $waitedMovieRepository,
        private readonly UserWaitedMovieRepository $userWaitedMovieRepository,
        private readonly UserService $userService
    ) {}

    public function getMovie(string $movieTitle): ?object
    {
        $movie = $this->waitedMovieRepository->findBy(['name' => $movieTitle]);

        return (!$movie) ? (null) : $movie[0];
    }

    public function getUserWaitedMovies(
        string $telegramId, bool $isMerged = true
    ): ?array
    {
        $userId = $this->userService->getUser($telegramId)->getId();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('wm.name, wm.release_date')
            ->from('App\Entity\UserWaitedMovie', 'uwm')
            ->leftJoin('App\Entity\WaitedMovie', 'wm',
                       'WITH', 'wm.id = uwm.waited_movie_id')
            ->where('uwm.user_id = :user_id')
            ->setParameter('user_id', $userId);

        $queryResult = $queryBuilder->getQuery()->getResult();
        return (!$isMerged) ? ($queryResult) :
            $this->getMergedUserWaitedMovies($queryResult);
    }

    public function getUserWaitedMovieTitles(
        string $telegramId, bool $isMerged = true
    ): ?array
    {
        $userId = $this->userService->getUser($telegramId)->getId();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('wm.name')
            ->from('App\Entity\UserWaitedMovie', 'uwm')
            ->leftJoin('App\Entity\WaitedMovie', 'wm',
                       'WITH', 'wm.id = uwm.waited_movie_id')
            ->where('uwm.user_id = :user_id')
            ->setParameter('user_id', $userId);

        $queryResult = $queryBuilder->getQuery()->getResult();
        return (!$isMerged) ? ($queryResult) :
            $this->getMergedUserWaitedMovies($queryResult, true);
    }

    public function isUserWaitedMovie(string $telegramId, string $movieTitle): bool
    {
        $userWaitedMovies = $this->getUserWaitedMovies($telegramId);

        return (in_array($movieTitle, $userWaitedMovies)) ? (true) : false;
    }

    public function addWaitedMovie(
        string $telegramId, string $movieTitle, string $date
    ): void
    {
        $waitedMovie = new WaitedMovie();
        $userWaitedMovie = new UserWaitedMovie();
        $user = $this->userService->getUser($telegramId);

        if (!$this->getMovie($movieTitle)) {
            $waitedMovie
                ->setName($movieTitle)
                ->setReleaseDate($this->getDate($date));

            $this->entityManager->persist($waitedMovie);
            $this->entityManager->flush();
        }

        $userWaitedMovie
            ->setUserId($user)
            ->setWaitedMovieId($waitedMovie);

        $user->addUserWaitedMovie($userWaitedMovie);

        $this->entityManager->persist($userWaitedMovie);
        $this->entityManager->flush();
    }

    public function removeWaitedMovie(string $telegramId, string $movieTitle): void
    {
        if (!$this->getMovie($movieTitle)) return;

        $userId = $this->userService->getUser($telegramId)->getId();
        $movieId = $this->getMovie($movieTitle)->getId();

        $userWaitedMovieId = $this->userWaitedMovieRepository->findOneBy(
            [
                'user_id' => $userId,
                'waited_movie_id' => $movieId
            ]
        );

        $this->entityManager->remove($userWaitedMovieId);
        $this->entityManager->flush();
    }

    protected function getDate(string $date): \DateTimeInterface
    {
        return \DateTime::createFromFormat('Y-m-d', $date);
    }

    protected function getDateString(\DateTimeInterface $date): string
    {
        return $date->format('d.m.Y');
    }

    protected function getMergedUserWaitedMovies(
        mixed $queryBuilderResult, bool $isOnlyNames = false
    ): ?array
    {
        $outputArray = [];
        foreach ($queryBuilderResult as $movie) {
            $movieTitle = $movie['name'];
            if (!$isOnlyNames) {
                $movieReleaseDate = $this->getDateString($movie['release_date']);

                array_push($outputArray, $movieTitle, $movieReleaseDate);
            } else {
                array_push($outputArray, $movie['name']);
            }
        }

        return $outputArray;
    }
}
