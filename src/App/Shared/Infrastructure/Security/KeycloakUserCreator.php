<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use App\Shared\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Mdg\KeycloakAuth\UserCreator\KeycloakUserCreatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class KeycloakUserCreator implements KeycloakUserCreatorInterface
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function createFromPayload(array $payload): UserInterface
    {
        $user = new User();
        $user->setEmail($payload['email']);
        $user->setName($payload['given_name']);
        $user->setSurname($payload['family_name']);
        $user->setRoles([User::ROLE_USER]);

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }
}
