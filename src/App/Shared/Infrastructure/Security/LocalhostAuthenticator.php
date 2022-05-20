<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use App\Shared\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Mdg\KeycloakAuth\Dto\TokenDto;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LocalhostAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public const USER_IDENTIFIER = 'admin@admin.com';

    public function __construct(
        private EntityManagerInterface $manager,
        private ContainerInterface     $container
    ) {
    }

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns false, the authenticator will be skipped.
     *
     * Returning null means authenticate() can be called lazily when accessing the token storage.
     */
    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $user = $this->createUser();
        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    private function createUser(): UserInterface
    {
        $user = $this->manager->getRepository(User::class)->findOneBy(['email' => self::USER_IDENTIFIER]);
        if (!$user) {
            $user = new User();
            $user->setRoles([User::ROLE_USER]);
            $user->setName('admin');
            $user->setSurname('admin');
            $user->setEmail(self::USER_IDENTIFIER);
            $this->manager->persist($user);
            $this->manager->flush();
        }

        return $user;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $tokenDto           = 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOiIxNjgwNjA0NzQ1In0.NBul2NcJ-LuAYELtEj4rQMtvn1ayz3aZuDbTnWHO0L8';

        $tokenCookie        = new Cookie($this->container->getParameter('keycloak_auth.auth_token_cookie_name'),
            $tokenDto);
        $tokenCookie        = $tokenCookie->withHttpOnly(false);
        $refreshTokenCookie = new Cookie($this->container->getParameter('keycloak_auth.auth_refresh_token_cookie_name'),
            $tokenDto);
        $refreshTokenCookie = $refreshTokenCookie->withHttpOnly(false);

        $response = new  RedirectResponse('/');
        $response->headers->setCookie($tokenCookie);
        $response->headers->setCookie($refreshTokenCookie);

        return $response;
    }
}