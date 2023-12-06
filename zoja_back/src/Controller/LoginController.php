<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_login')]
    public function Login(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        $rememberMe = $data['remember'];
        //search in database if user exists
        $user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

        //check if email and pass are correct
        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid password or email', 'code_response'=>Response::HTTP_NOT_FOUND]);
        }

        //generate token depending if remember me is checked
        if($rememberMe === false) {
            $message = 'false';
            $token = $jwtManager->create($user);
        } else {
            $message = 'true';
            $token = $jwtManager->createFromPayload($user, ['exp' => time() + 432000]);
        }

        return $this->json([
            'message' => $message,
            'token' => $token,
            'code_response'=>Response::HTTP_OK
        ]);
    }

    #[Route('/api/me', name: 'app_me', methods: ['POST'])]
    public function me(Request $request, EntityManagerInterface $entityManager):JsonResponse {
        $data = json_decode($request->getContent(), true);
        $user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $data['email']]);
        $posts = [];
        foreach ($user->getUserPosts() as $post) {
            $posts [] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'author' => $post->getAuthor(),
                'content' => $post->getContent(),
                'creation_date' => $post->getCreationDate()
            ];
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'authentification_name' => $user->getAuthentificationName(),
            'roles' => $user->getRoles(),
            'profile_picture' => $user->getProfilePicture(),
            'posts' => $posts
        ]);
    }
}
