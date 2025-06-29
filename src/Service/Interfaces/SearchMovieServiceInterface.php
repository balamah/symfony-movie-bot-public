<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

interface SearchMovieServiceInterface
{
    public function searchMovie(string $movieTitle): ?array;
}
