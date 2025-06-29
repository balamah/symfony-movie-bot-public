<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

interface WaitedMovieServiceInterface 
{
    public function isUserWaitedMovie(string $telegramId, string $movieTitle): bool;

    public function getUserWaitedMovies(string $telegramId): ?array;

    public function addWaitedMovie(
        string $telegramId, string $movieTitle, string $date
    ): void;

    public function removeWaitedMovie(string $telegramId, string $movieTitle): void;
}
