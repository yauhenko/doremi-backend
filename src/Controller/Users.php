<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Yabx\WebToken\TokenManager;
use App\Models\Users\AuthRequest;
use App\Repository\UserRepository;
use App\Models\Users\RegisterRequest;
use App\Security\WebTokenAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Yabx\TypeScriptBundle\Attributes\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yabx\TypeScriptBundle\Attributes\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Controller]
#[Route('users')]
class Users extends Base {

    #[Method(request: RegisterRequest::class, response: User::class)]
    #[Route(methods: ['POST'])]
    public function register(RegisterRequest $request, UserRepository $userRepository, EntityManagerInterface $em,
                             UserPasswordHasherInterface $hasher): JsonResponse {

        if($userRepository->count(['email' => $request->email])) {
            return $this->error('Аккаунт с указанным E-mail уже есть в системе');
        }

        if($request->role === UserRole::Admin && !$this->isGranted('ROLE_ADMIN')) {
            return $this->error('В доступе отказано', 403);
        }

        $user = new User($request->email, $request->role);
        $user->setPassword($hasher->hashPassword($user, $request->password));
        $em->persist($user);
        $em->flush();

        return $this->result($user, Response::HTTP_OK, ['user:full']);

    }

    #[Method(request: AuthRequest::class, response: '{ token: string, user: IUser }')]
    #[Route('/auth', methods: ['POST'])]
    public function auth(AuthRequest $request, UserRepository $userRepository,
                             UserPasswordHasherInterface $hasher, TokenManager $tokenManager): JsonResponse {

        if(!$user = $userRepository->findOneBy(['email' => $request->email])) {
            return $this->error('Аккаунт не найден', 403);
        }

        if(!$hasher->isPasswordValid($user, $request->password)) {
            return $this->error('В доступе отказано', 403);
        }

        $token = $tokenManager->issue($user->getId(), [
            'role' => $user->getRole()
        ]);

        return $this->result(['token' => $token->getToken(), 'user' => $user], Response::HTTP_OK, ['user:full']);

    }

    #[Method(response: '{ token: string, user: IUser }')]
    #[Route('/refresh', methods: ['POST'])]
    public function refreshToken(WebTokenAuthenticator $authenticator, UserRepository $userRepository, TokenManager $tokenManager): JsonResponse {

        if(!$token = $authenticator->getToken()) {
            return $this->error('Not authenticated', 401);
        }

        $token = $tokenManager->refresh($token);
        $user = $userRepository->find($token->getUser());

        return $this->result(['token' => $token->getToken(), 'user' => $user], Response::HTTP_CREATED, ['user:full']);

    }

}
