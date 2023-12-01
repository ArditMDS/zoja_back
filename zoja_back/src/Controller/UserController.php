<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/register', name: 'app_create_user', methods: ['POST'])]
    public function index(EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $verifyEmail = $entityManager->getRepository(Users::class)->findOneBy(['email' => $data['email']]);
        $verifyId = $entityManager->getRepository(Users::class)->findOneBy(['authentification_name' => $data['auth_name']]);
        if($verifyEmail) {
            return new JsonResponse(['message'=>'Votre email est déja utilisé', 'code_response'=>Response::HTTP_BAD_REQUEST]);
        } else if($verifyId) {
            return new JsonResponse(['message'=>'Votre ID est déja utilisé', 'code_response'=>Response::HTTP_BAD_REQUEST]);
        }
        $user = new Users();
        $user->setAuthentificationName($data['auth_name']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setRoles($data['roles']);
        $newPassword = $data['password'];
        $hashedPass = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPass);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['code_response'=>Response::HTTP_OK]);
    }
}
