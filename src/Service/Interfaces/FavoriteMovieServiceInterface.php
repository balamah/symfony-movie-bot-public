<?php

namespace App\Service\Interfaces;

interface FavoriteMovieServiceInterface
{
    public function isUserFavoriteMovie(string $telegramId, string $movieTitle): bool;

    public function getUserFavoriteMovies(string $telegramId): ?array;

    public function addFavoriteMovie(string $telegramId, string $movieTitle): void;

    public function removeFavoriteMovie(string $telegramId, string $movieTitle): void;
}
