<?php
declare(strict_types=1);

namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Entity\User;
use App\Security\LocalhostAuthenticator;
use Codeception\Module\Symfony;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Unit extends \Codeception\Module
{
    public function replaceServiceWithMock(string $serviceClassName, MockObject $mockedService): void
    {
        /** @var Symfony $symfony */
        $symfony = $this->getModule('Symfony');
        $symfony->kernel->getContainer()->set($serviceClassName, $mockedService);
        $symfony->persistService($serviceClassName);
    }

    public function restoreMockedService(string $serviceClassName): void
    {
        /** @var Symfony $symfony */
        $symfony = $this->getModule('Symfony');
        $symfony->unpersistService($serviceClassName);
    }

    public function loginAs(string $role = User::ROLE_MANAGER): void
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => LocalhostAuthenticator::USER_IDENTIFIER]);
        $user->setRoles([$role]);
        /** @var Symfony $symfony */
        $symfony = $this->getModule('Symfony');
        /* @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $symfony->_getContainer()->get('security.token_storage');
        $tokenStorage->setToken(
            new UsernamePasswordToken($user, $user->getEmail(), 'main', $user->getRoles()));
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->getModule('Doctrine2')->_getEntityManager();
    }
}
