<?php

namespace App\Security;

use App\Entity\User;
use Yabx\WebToken\Token;
use App\Utils\RequestUtil;
use Yabx\WebToken\TokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

class WebTokenAuthenticator extends AbstractAuthenticator {

    protected TokenManager $tokenManager;
    protected EntityManagerInterface $em;
    protected ?Token $token = null;

    public function __construct(EntityManagerInterface $em, TokenManager $tokenManager) {
        $this->tokenManager = $tokenManager;
        $this->em = $em;
    }

    public function supports(Request $request): ?bool {
        $token = $this->getAuthToken($request);
        return $token && str_starts_with($token, TokenManager::SIGNATURE);
    }

    public function getAuthToken(Request $request): ?string {
        return str_replace('Bearer ', '', $request->headers->get('authorization') ?? '') ?: $request->get('auth');
    }

    public function authenticate(Request $request): Passport {
        if(!$token = $this->getAuthToken($request)) {
            throw new CustomUserMessageAuthenticationException('No Web Token provided');
        }
        $this->token = $this->tokenManager->read($token);
        $user = $this->em->find(User::class, $this->token->getUser());

        //$silent = (bool)$this->token->getPayload()['silent'];
        $lockIp = $this->token->getPayload()['ip'] ?? null;

        $ip = RequestUtil::getClientIp($request);

        if($lockIp && $lockIp !== $ip) {
            throw new CustomUserMessageAuthenticationException('Session IP changed. Please login again');
        }

//        if(!$silent) {
////            $user->updateAccessedAt();
////            $user->setIp($ip);
//        }
//
//        $this->em->flush();

        return new Passport(new UserBadge($user->getEmail(), fn() => $user), new CustomCredentials(fn() => true, $token));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }

    public function getToken(): ?Token {
        return $this->token;
    }

}
