<?php

namespace App\Service\Interfaces;

use Luzrain\TelegramBotApi\Type;

interface UserServiceInterface 
{
    public function getUser(string $telegramId): ?object;

    public function addUser(Type\Chat $userData): void;

    public function banUser(string $telegramId): void;

    public function unbanUser(string $telegramId): void;

    public function log(Type\Chat $userData, string $action): void;
}
