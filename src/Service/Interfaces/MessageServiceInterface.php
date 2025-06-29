<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

use Luzrain\TelegramBotApi\Type\InlineKeyboardMarkup;
use Luzrain\TelegramBotApi\Type\ReplyKeyboardMarkup;
use Luzrain\TelegramBotApi\Type\Message;
use Luzrain\TelegramBotApi\Method;

/**
 * @package App\Service\Interfaces
 */
interface MessageServiceInterface
{
    public function getMessage(
        Message $message, string $text,
        ReplyKeyboardMarkup|InlineKeyboardMarkup $markup = null
    ): Method;

    public function formatFavoriteMoviesArray(array $userFavoriteMovies): ?string;

    public function formatWaitedMoviesArray(array $userWaitedMovies): ?string;
}
