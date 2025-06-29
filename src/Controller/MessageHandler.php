<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FavoriteMovieService;
use App\Service\WaitedMovieService;
use App\Service\SearchMovieService;
use App\Service\MessageService;
use App\Service\SessionService;
use App\Service\UserService;

use App\Message\Keyboards;

use Luzrain\TelegramBotBundle\Attribute\OnEvent;
use Luzrain\TelegramBotBundle\TelegramCommand;
use Luzrain\TelegramBotApi\Method;
use Luzrain\TelegramBotApi\Event;
use Luzrain\TelegramBotApi\Type;

final class MessageHandler extends TelegramCommand
{
    public function __construct(
        private readonly UserService $userService,
        private readonly FavoriteMovieService $favoriteMovieService,
        private readonly WaitedMovieService $waitedMovieService,
        private readonly SessionService $sessionService,
        private readonly MessageService $messageService,
        private readonly SearchMovieService $searchMovieService
    ) {}
    
    #[OnEvent(Event\Message::class)]
    public function __invoke(Type\Message $message): Method
    {
        $movieSearchArray = $this->sessionService->getVariable(
        	(string) $message->chat->id, 'movie-search-array'
        );

        if ($movieSearchArray && $message->text != '❌ Скасувати') {
            return $this->getSearchedMovieMessage($message, $movieSearchArray);
        }

        try {
            switch ($message->text) {
                case '❤ Улюблені фільми':
                    $replyMessage = $this->getFavoriteMovies($message);
                    break;

                case '📜 Список очікування':
                    $replyMessage = $this->getWaitedMovies($message);
                    break;

                case '➕ Додати очікуваний фільм':
                    $this->sessionService->setVariable(
                        (string) $message->chat->id, 'last-message', 'add-waited-movie'
                    );

                    $text = 'Введи назву фільма і дату його у форматі
    НАЗВА ФІЛЬМУ|ДАТА ВИХОДУ (рррр-мм-дд)';

                    $replyMessage = $this->messageService->getMessage(
                        $message, $text, Keyboards::getCancelKeyboard()
                    );

                    break;

                case '❌ Видалити очікуваний фільм':
                    $this->sessionService->setVariable(
                        (string) $message->chat->id, 'last-message', 'remove-waited-movie'
                    );

                    $text = 'Введи назву очікуваного фільму для видалення';

                    $replyMessage = $this->messageService->getMessage(
                        $message, $text, Keyboards::getCancelKeyboard()
                    );

                    break;

                case '🔍 Пошук фільму':
                    $this->sessionService->setVariable(
                        (string) $message->chat->id, 'last-message', 'search-movie'
                    );

                    $text = 'Введи назву фільму для пошуку';

                    $replyMessage = $this->messageService->getMessage(
                        $message, $text, Keyboards::getCancelKeyboard()
                    );

                    break;

                case '⬅ Повернутись до головного меню':
                    $replyMessage = $this->messageService->getMessage(
                        $message, '✅ Повернуто до головного меню',
                        Keyboards::getStartKeyboard($message)
                    );

                    break;

                case '➕ Додати улюблений фільм':
                    $this->sessionService->setVariable(
                        (string) $message->chat->id, 'last-message', 'add-favorite-movie'
                    );

                    $replyMessage = $this->messageService->getMessage(
                        $message, 'Введи назву фільму для додання у список улюблених',
                        Keyboards::getCancelKeyboard()
                    );

                    break;

                case '❌ Видалити улюблений фільм':
                    $this->sessionService->setVariable(
                        (string) $message->chat->id, 'last-message', 'remove-favorite-movie'
                    );

                    $replyMessage = $this->messageService->getMessage(
                        $message, 'Введи назву улюбленого фільму для видалення',
                        Keyboards::getCancelKeyboard()
                    );

                    break;

                case '❌ Скасувати':
                    $this->sessionService->setVariable(
                        (string) $message->chat->id, 'last-message', 'cancel'
                    );

                    $this->sessionService->unsetVariable(
                        (string) $message->chat->id, 'movie-search-array'
                    );

                    $replyMessage = $this->messageService->getMessage(
                        $message, '✅ Скасовано',
                        Keyboards::getStartKeyboard($message)
                    );

                    break;

                default:
                    $variable = $this->sessionService->getVariable(
                        (string) $message->chat->id, 'last-message'
                    );

                    if (isset($_SESSION)) {
                        $replyMessage = $this->processMessage($message, $variable);
                    } else {
                        $replyMessage = $this->getMessageUndefinedEvent($message);
                    }

                    break;
            }
        } catch (\Throwable $e) {
            echo "$e\n";

            $this->userService->addUser($message->chat);

            $replyMessage = $this->messageService->getMessage(
                $message, 'Сталась якась помилка'
            );
        }

        $logMessage = "has entered message: {$message->text}";
        $this->userService->log($message->chat, $logMessage);

        return $replyMessage;
    }

    protected function processMessage(
        Type\Message $message, string $lastMessage
    ): Method
    {
        switch ($lastMessage) {
            case 'add-favorite-movie':
                $text = $this->addFavoriteMovie($message);

                $replyMessage = $this->messageService->getMessage(
                    $message, $text, Keyboards::getFavoriteMoviesKeyboard()
                );

                $this->sessionService->setVariable(
                    (string) $message->chat->id, 'last-message', 'start'
                );

                break;

            case 'remove-favorite-movie':
                $text = $this->removeFavoriteMovie($message);

                $replyMessage = $this->messageService->getMessage(
                    $message, $text, Keyboards::getFavoriteMoviesKeyboard()
                );

                $this->sessionService->setVariable(
                    (string) $message->chat->id, 'last-message', 'start'
                );

                break;

            case 'add-waited-movie':
                $text = $this->addWaitedMovie($message);

                $replyMessage = $this->messageService->getMessage(
                    $message, $text, Keyboards::getWaitListKeyboard()
                );

                $this->sessionService->setVariable(
                    (string) $message->chat->id, 'last-message', 'start'
                );

                break;

            case 'remove-waited-movie':
                $text = $this->removeWaitedMovie($message);

                $replyMessage = $this->messageService->getMessage(
                    $message, $text, Keyboards::getWaitListKeyboard()
                );

                $this->sessionService->setVariable(
                    (string) $message->chat->id, 'last-message', 'start'
                );

                break;

            case 'search-movie':
                $text = $this->searchMovies($message);

                $replyMessage = $this->messageService->getMessage(
                    $message, $text, Keyboards::getMovieSearchKeyboard()
                );

                break;

            default:
                $replyMessage = $this->getMessageUndefinedEvent($message);

                break;
        }

        return $replyMessage;
    }

    protected function getMessageUndefinedEvent(Type\Message $message): Method
    {
        $text = (string) 'Неопізнана дія: ' . $message->text;
        return $this->messageService->getMessage(
            $message, $text,
            Keyboards::getStartKeyboard($message)
        );
    }

    protected function getFavoriteMovies(Type\Message $message): Method
    {
        $favoriteMoviesArray = $this->favoriteMovieService->getUserFavoriteMovies(
            (string) $message->chat->id
        );

        $favoriteMoviesText = $this->messageService->formatFavoriteMoviesArray(
            $favoriteMoviesArray
        );
        
        return $this->messageService->getMessage(
            $message, $favoriteMoviesText, Keyboards::getFavoriteMoviesKeyboard()
        );
    }

    protected function getWaitedMovies(Type\Message $message): Method
    {
        $waitedMoviesArray = $this->waitedMovieService->getUserWaitedMovies(
            (string) $message->chat->id
        );

        $waitedMoviesText = $this->messageService->formatWaitedMoviesArray(
             $waitedMoviesArray
        );

        return $this->messageService->getMessage(
            $message, $waitedMoviesText, Keyboards::getWaitListKeyboard()
        );
    }

    protected function addFavoriteMovie(Type\Message $message): string
    {
        $telegramId = (string) $message->chat->id;
        $movieTitle = $message->text;

        $text = '✅ Фільм: *' . $movieTitle . '* додано в улюблені фільми';

        if (!$this->favoriteMovieService->isUserFavoriteMovie($telegramId, $movieTitle))
        {
            $this->favoriteMovieService->addFavoriteMovie($telegramId, $movieTitle);
        } else {
            $text = '❌ Цей фільм вже є в улюбленому списку';
        }

        return $text;
    }

    protected function removeFavoriteMovie(Type\Message $message): string
    {
        $telegramId = (string) $message->chat->id;
        $movieTitle = $message->text;

        $text = '✅ Фільм: *' . $movieTitle . '* успішно видалено з улюблених';

        if ($this->favoriteMovieService->isUserFavoriteMovie($telegramId, $movieTitle))
        {
            $this->favoriteMovieService->removeFavoriteMovie($telegramId, $movieTitle);
        } else {
            $text = '❌ Фільм не є улюбленим';
        }

        return $text;
    }

    protected function addWaitedMovie(Type\Message $message): string
    {
        $telegramId = (string) $message->chat->id;

        $movieForm = explode('|', $message->text);
        $movieTitle = $movieForm[0];
        $movieReleaseDate = $movieForm[1];

        $text = '✅ Фільм *' . $movieTitle .
            '* очікується на *' . $movieReleaseDate . '*';

        if (!$this->waitedMovieService->isUserWaitedMovie($telegramId, $movieTitle))
        {
            $this->waitedMovieService->addWaitedMovie(
                $telegramId, $movieTitle, $movieReleaseDate
            );
        } else {
            $text = '❌ Цей фільм вже є у списку очікування';
        }

        return $text;
    }

    protected function removeWaitedMovie(Type\Message $message): string
    {
        $telegramId = (string) $message->chat->id;
        $movieTitle = $message->text;

        $text = '✅ Фільм *' . $movieTitle . '* успішно видалено з списку очікування';

        if ($this->waitedMovieService->isUserWaitedMovie($telegramId, $movieTitle))
        {
            $this->waitedMovieService->removeWaitedMovie($telegramId, $movieTitle);
        } else {
            $text = '❌ Цього фільму немає у списку очікування';
        }

        return $text;
    }

    protected function searchMovies(Type\Message $message): string
    {
        $movies = $this->searchMovieService->searchMovie($message->text);
        
        if (!$movies) {
            $text = '❌ Нічого не знайдено(((';
        } else {
            $this->sessionService->setVariable(
                (string) $message->chat->id, 'movie-search-array', $movies
            );

            $moviesArrayLength = count($movies);
            $text = "✅ Знайдено $moviesArrayLength фільмів.
Для продовдження, натискай кнопку *✅ Продовжити пошук* або присилай будь-яке повідомлення.
Щоб закінчити пошук, натисни на кнопку *❌ Скасувати* або напиши /start";
        }

        return $text;
    }

    protected function getSearchedMovieMessage(
        Type\Message $message, array $movieSearchArray
    ): Method
    {
        $index = $this->sessionService->getVariable(
            (string) $message->chat->id, 'movie-search-index'
        );

        if (!$index) {
            $index = 0;
        }

        $nextIndex = $index + 1;

        if ($nextIndex == count($movieSearchArray)) {
            $this->sessionService->unsetVariable(
                (string) $message->chat->id, 'movie-search-array'
            );

            $this->sessionService->setVariable(
            	(string) $message->chat->id, 'last-message', 'start'
            );

            return $this->messageService->getMessage(
                $message, '✅ Пошук завершено', Keyboards::getStartKeyboard($message)
            );
        } else {
            $this->sessionService->setVariable(
                (string) $message->chat->id, 'movie-search-index', $nextIndex
            );
        }

        $movie = $movieSearchArray[$index];
        $movieString = $movie['string'];
        $movieImage = $movie['image'];

        return $this->messageService->getPhotoMessage(
            $message, $movieImage, $movieString
        );
    }
}
