<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Interfaces\MessageServiceInterface;

use Luzrain\TelegramBotApi\Type\InlineKeyboardMarkup;
use Luzrain\TelegramBotApi\Type\ReplyKeyboardMarkup;
use Luzrain\TelegramBotApi\Type\Message;
use Luzrain\TelegramBotApi\Method;

/**
 * @package App\Service
 */
class MessageService implements MessageServiceInterface
{
    public function getMessage(
        Message $message, string $text,
        ReplyKeyboardMarkup|InlineKeyboardMarkup $markup = null
    ): Method
    {
        if ($markup) {
            return new Method\SendMessage(
                parseMode: 'markdown',
            	chatId: $message->chat->id,
            	text: $text,
                replyMarkup: $markup
            );
        }

        return new Method\SendMessage(
            parseMode: 'markdown',
            chatId: $message->chat->id,
            text: $text
        );
    }

    public function getPhotoMessage(
        Message $message, string $photo, string $caption = null
    ): Method
    {
        if (!$caption) {
            return new Method\SendPhoto(
                chatId: $message->chat->id,
                photo: $photo
            );
        }

        return new Method\SendPhoto(
            chatId: $message->chat->id,
            photo: $photo,
            caption: $caption
        );
    }

    public function formatFavoriteMoviesArray(array $userFavoriteMovies): ?string
    {
        if (!$userFavoriteMovies) return '❌ У тебе немає улюблених фільмів';

        $outputString = "Твої улюблені фільми\n";
        foreach ($userFavoriteMovies as $movie) {
            $outputString .= '- ' . $movie . "\n";
        }

        return $outputString;
    }

    public function formatWaitedMoviesArray(array $userWaitedMovies): ?string
    {
        if (!$userWaitedMovies) return '❌ У тебе ще немає очікуваних фільмів';

        $outputString = "Твої очікувані фільми\n";

        for ($i = 0; $i < count($userWaitedMovies); $i++)
        {
            if ($i % 2 == 0) {
                $movieTitle = $userWaitedMovies[$i];
                $movieReleaseDate = $userWaitedMovies[$i + 1];

                $outputString .= '- ' . $movieTitle . ' --> '
                    . $movieReleaseDate . "\n";
            }
        }

        $outputString .= "\n*УВАГА: бот поки що не може повідомляти про вихід фільмів*";

        return $outputString;
    }
}
