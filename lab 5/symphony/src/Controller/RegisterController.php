<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
      Request $request,
      UserPasswordHasherInterface $passwordHasher,
      EntityManagerInterface $em,
      UserRepository $userRepository
  ): JsonResponse {
      $data = json_decode($request->getContent(), true);
  
      $email = $data['email'] ?? null;
      $plainPassword = $data['password'] ?? null;
      $roles = $data['roles'] ?? ['ROLE_USER'];
  
      if (!$email || !$plainPassword) {
          return new JsonResponse(['error' => 'Email and password are required.'], 400);
      }
  
      if ($userRepository->findOneBy(['email' => $email])) {
          return new JsonResponse(['error' => 'User already exists.'], 409);
      }
  
      $user = new User();
      $user->setEmail($email);
      $user->setRoles($roles);
      $user->setName($data['name'] ?? null);
      $user->setRole($roles[0] ?? 'ROLE_USER');
      $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
      $user->setPassword($hashedPassword);
  
      $em->persist($user);
      $em->flush();
  
      return new JsonResponse(['message' => 'User registered successfully.'], 201);
  }
}
