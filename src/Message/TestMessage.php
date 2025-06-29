<?php

declare(strict_types=1);

namespace App\Message;

use App\Message\Interfaces\MessageHighInterface;

/**
 * @package App\Message
 */
class TestMessage implements MessageHighInterface
{
    public function __construct(
        private readonly string $someId
    ) {
    }

    public function getSomeId(): string
    {
        return $this->someId;
    }
}
