<?php
declare(strict_types=1);

namespace App\Shared\Application\Query\getCurrentUser;

use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Entity\User;
use Symfony\Component\Security\Core\Security;

class GetCurrentUserQueryHandler implements QueryHandlerInterface
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(GetCurrentUserQuery $query): UserReadModel
    {
        /* @var User $user */
        $user = $this->security->getUser();

        return new UserReadModel($user->getId(),$user->getName(), $user->getSurname(), $user->getEmail(), $user->getRoles());
    }
}