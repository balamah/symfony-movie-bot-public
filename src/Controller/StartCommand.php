<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\MessageService;
use App\Service\SessionService;
use App\Service\UserService;

use App\Message\Keyboards;

use Luzrain\TelegramBotBundle\Attribute\OnCommand;
use Luzrain\TelegramBotBundle\TelegramCommand;
use Luzrain\TelegramBotApi\Method;
use Luzrain\TelegramBotApi\Type;

final class StartCommand extends TelegramCommand
{
    public function __construct(
        private readonly UserService $userService,
        private readonly SessionService $sessionService,
        private readonly MessageService $messageService
    ) {}
    
    #[OnCommand('/start', description: 'Розпочати роботу бота', publish: true)]
    public function __invoke(Type\Message $message): Method
    {
        $this->sessionService->setVariable(
            (string) $message->chat->id, 'last-message', 'start'
        );

        $this->userService->addUser($message->chat);
        $this->userService->log($message->chat, 'has started the bot');
        
        $text = "👋🏻 Привіт *{$message->chat->firstName}*";

        return $this->messageService->getMessage(
            $message, $text, Keyboards::getStartKeyboard($message)
        );
    }
}
