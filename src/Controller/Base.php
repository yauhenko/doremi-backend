<?php

namespace App\Controller;

use App\Entity\User;
use App\Utils\RequestUtil;
use App\Security\WebTokenAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Yabx\RestBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

abstract class Base extends RestController {

    protected RequestStack $requestStack;
    protected Security $security;
    protected WebTokenAuthenticator $authenticator;
    protected EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, WebTokenAuthenticator $authenticator, EntityManagerInterface $em) {
        $this->requestStack = $requestStack;
        $this->authenticator = $authenticator;
        $this->em = $em;
    }

    public function getUser(): User {
        if(!$user = parent::getUser())
            throw new AuthenticationException('Authorization required', 401);
        assert($user instanceof User);
        return $user;
    }

    public function getClientIp(): string {
        return RequestUtil::getClientIp($this->requestStack->getCurrentRequest());
    }

    public function getClientCountry(): string {
        return RequestUtil::getClientCountry($this->requestStack->getCurrentRequest());
    }

    protected function httpError(int $code, ?string $message = null): Response {
        $res = new Response();
        $res->setStatusCode($code);
        return $this->render('error.twig', [
            'message' => $message ?? Response::$statusTexts[ $code ] ?? 'Unexpected Error',
            'code' => $code
        ], $res);
    }

}
