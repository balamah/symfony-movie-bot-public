<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Interfaces\SessionServiceInterface;

class SessionService implements SessionServiceInterface
{
    public function setVariable(
        string $telegramId, string $variable, mixed $value
    ): void
    {
        $_SESSION[$telegramId][$variable] = $value;
    }

    public function unsetVariable(string $telegramId, string $variable): void
    {
        unset($_SESSION[$telegramId][$variable]);
    }

    public function getVariable(string $telegramId, string $variable): mixed
    {
        return (isset($_SESSION[$telegramId][$variable])) ?
            ($_SESSION[$telegramId][$variable]) : null;
    }
}
