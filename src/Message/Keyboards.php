<?php

declare(strict_types=1);

namespace App\Message;

use Luzrain\TelegramBotApi\Type\KeyboardButtonArrayBuilder;
use Luzrain\TelegramBotApi\Type\ReplyKeyboardMarkup;
use Luzrain\TelegramBotApi\Type;

class Keyboards
{
    protected static function getKeyboard(array $buttons): ReplyKeyboardMarkup
    {
        $keyboard = KeyboardButtonArrayBuilder::create();

        foreach ($buttons as $buttonText) {
        	$keyboard->addButton(new Type\KeyboardButton(text: $buttonText));
        }

        $outputKeyboard = new ReplyKeyboardMarkup(
            resizeKeyboard: true,
            keyboard: $keyboard
        );

        return $outputKeyboard;
    }

    public static function getStartKeyboard(Type\Message $message): ReplyKeyboardMarkup
    {
        $buttons = ['🔍 Пошук фільму',
                    '❤ Улюблені фільми',
                    '📜 Список очікування'];

        if ($message->chat->id == $_ENV['BOT_ADMIN_ID']) {
            array_push($buttons, '# Admin keyboard');
        }

        return self::getKeyboard($buttons);
    }
    
    public static function getAdminKeyboard(Type\Message $message): ?ReplyKeyboardMarkup
    {
        if ($message->chat->id != $_ENV['BOT_ADMIN_ID']) {
            return null;
        }
       
        $buttons = ["🔔 Send global notification",
                    "🔨 Ban",
                    '🛟 Unban', 
                    '💬 Message',
                    '📜 Get users',
                    '📜 Get banned users',
                    '⬅ Повернутись до головного меню'];

        return self::getKeyboard($buttons);
    }

    public static function getWaitListKeyboard(): ReplyKeyboardMarkup
    {
        $buttons = ['➕ Додати очікуваний фільм',
                    '❌ Видалити очікуваний фільм',
                    '⬅ Повернутись до головного меню'];

        return self::getKeyboard($buttons);
    }

    public static function getFavoriteMoviesKeyboard(): ReplyKeyboardMarkup
    {
        $buttons = ['➕ Додати улюблений фільм',
                    '❌ Видалити улюблений фільм',
                    '⬅ Повернутись до головного меню'];

        return self::getKeyboard($buttons);
    }

    public static function getCancelKeyboard(): ReplyKeyboardMarkup
    {
        $buttons = ['❌ Скасувати'];

        return self::getKeyboard($buttons);
    }

    public static function getMovieSearchKeyboard(): ReplyKeyboardMarkup
    {
        $buttons = ['✅ Продовжити пошук', '❌ Скасувати'];

        return self::getKeyboard($buttons);
    }
}
