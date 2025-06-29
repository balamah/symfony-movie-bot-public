<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Interfaces\UserServiceInterface;
use App\Repository\UserRepository;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;

use Luzrain\TelegramBotApi\Type;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {}

    public function getUser(string $telegramId): ?object
    {
        $user = $this->userRepository->findBy(['telegram_id' => $telegramId]);

        if (!$user) {
            return null;
        }

        return $user[0];
    }

    public function addUser(Type\Chat $userData): void
    {
        $user = new User();

        if (!$this->getUser((string) $userData->id)) {
            $user->setTelegramId((string) $userData->id)
                ->setFirstName($userData->firstName)
                ->setLastName($userData->lastName)
                ->setUsername($userData->username)
                ->setCreatedAt(new \DateTime());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public function banUser(string $telegramId): void
    {
        $user = $this->getUser($telegramId);

        $user->setBanned(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
    
    public function unbanUser(string $telegramId): void
    {
        $user = $this->getUser($telegramId);

        $user->setBanned(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function log(Type\Chat $userData, string $action): void
    {
        $currentDatetime = date('d.m.Y H:i');

        if (!$userData->username) {
            $name = $userData->firstName;
        } else {
            $name = '@' . $userData->username;
        }

        $log = "$currentDatetime $name $action\n";
        $this->writeLog($log);
        echo "$log";
    }

    protected function writeLog(string $text): void
    {
        $file = fopen('../../users.log', 'a+');
        fwrite($file, $text);
        fclose($file);
    }
}
