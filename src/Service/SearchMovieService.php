<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Interfaces\SearchMovieServiceInterface;

set_error_handler(function(int $errno, string $errstr) {
    if ((strpos($errstr, 'Undefined array key') === false) &&
        (strpos($errstr, 'Trying to access ') === false)) {
        return false;
    } else {
        return true;
    }
}, E_WARNING);

class SearchMovieService implements SearchMovieServiceInterface
{
    protected object $curl;
    
    public function __construct()
    {
        $this->curl = curl_init();
    }

    public function searchMovie(string $movieTitle): ?array
    {
        $urlMovieTitle = $this->getUrlFormattedMovieTitle($movieTitle);
        $url = '/auto-complete?q=' . $urlMovieTitle;
        $movies = $this->requestBasicImdb($url)['d'];
        $output = [];
        foreach ($movies as $movie)
        {
            $id = array_search($movie, $movies);

            $name = $movie['l'];
            $year = (string) $movie['y'];
            $director = $movie['s'];
            $tconst = $movie['id'];

            $emptyImageUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png';
            $imageUrl = $movie['i']['imageUrl'];

            $rating = $this->getMovieRating($tconst);
            $string = $this->getMovieString($name, $year, $director, $rating);
            
            if ($movie['yr']) {
                $string = $string . ". Це серіал" . $movie['yr'];
            }

            $output[$id]['string'] = $string;
            $output[$id]['image'] = (!$imageUrl) ? $emptyImageUrl : $imageUrl;
        }

        return $output;
    }
    
    protected function getUrlFormattedMovieTitle(string $movieTitle): string
    {
        $output = str_replace(' ', '%20', $movieTitle);

        return $output;
    }

    protected function getRequestOutput(): ?array
    {
        $response = curl_exec($this->curl);
        $error = curl_error($this->curl);

        curl_close($this->curl);

        if ($error) {
            throw new \Exception("cURL Error #:" . $error);
        }

        $output = json_decode($response, true);
        return $output;
    }

    protected function requestBasicImdb(string $url): ?array
    {
        curl_setopt_array($this->curl, [
        	CURLOPT_URL => $_ENV['API_IMDB_BASE_URL'] . $url,
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_ENCODING => "",
        	CURLOPT_MAXREDIRS => 10,
        	CURLOPT_TIMEOUT => 30,
        	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	CURLOPT_CUSTOMREQUEST => "GET",
        	CURLOPT_HTTPHEADER => [
        		"X-RapidAPI-Host: imdb8.p.rapidapi.com",
        		"X-RapidAPI-Key: " . $_ENV['API_IMDB_KEY']
        	],
        ]);

        return $this->getRequestOutput();
    }

    protected function requestRatingImdb(string $tconst): array
    {
        curl_setopt_array($this->curl, [
            CURLOPT_URL => $_ENV['API_IMDB_RATING_URL'] . '/ratings?id=' . $tconst,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: movies-ratings2.p.rapidapi.com",
                "X-RapidAPI-Key: " . $_ENV['API_IMDB_KEY']
            ],
        ]);

        return $this->getRequestOutput();
    }

    protected function getMovieRating(string $tconst): ?string
    {
        $url = '/title/v2/get-ratings?tconst=' . $tconst;
        $requestOutput = $this->requestBasicImdb($url);
        if (!$requestOutput) {
            return null;
        }

        $output = $requestOutput['data']['title']['ratingsSummary']['aggregateRating'];

        return strval($output);
    }

    protected function getMovieString(
        ?string $name, ?string $year, ?string $director, ?string $rating
    ): ?string
    {
        if (!$rating) {
            $output = "$name ($year), $director";
        } else {
            $output = "$name ($year) ⭐$rating, $director";
        }

        return $output;
    }
}
