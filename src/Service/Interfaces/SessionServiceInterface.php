<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

interface SessionServiceInterface 
{
    public function setVariable(
        string $telegramId, string $variable, mixed $value
    ): void;

    public function unsetVariable(string $telegramId, string $variable): void;

    public function getVariable(string $telegramId, string $variable): mixed;
}
