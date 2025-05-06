<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/api/client')]
final class ClientController extends AbstractController
{
    #[Route(name: 'api_client_index', methods: ['GET'])]
    public function index(
        ClientRepository $clientRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $clients = $clientRepository->findAll();

        return new JsonResponse(
            $serializer->serialize($clients, 'json', ['groups' => 'client:read']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_client_show', methods: ['GET'])]
    public function show(int $id, ClientRepository $clientRepository, SerializerInterface $serializer): JsonResponse
    {
        $client = $clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $serializer->serialize($client, 'json', ['groups' => 'client:read']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/new', name: 'api_client_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $client = new Client();
        $client->setName($data['name']);
        $client->setEmail($data['email']);
        $client->setPhone($data['phone']);

        $entityManager->persist($client);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($client, 'json', ['groups' => 'client:read']),
            JsonResponse::HTTP_CREATED,
            [],
            true
        );
    }

    #[Route('/{id}/edit', name: 'api_client_edit', methods: ['PUT'])]
    public function edit(
        int $id,
        Request $request,
        ClientRepository $clientRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        $client = $clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $client->setName($data['name'] ?? $client->getName());
        $client->setEmail($data['email'] ?? $client->getEmail());
        $client->setPhone($data['phone'] ?? $client->getPhone());

        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($client, 'json', ['groups' => 'client:read']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_client_delete', methods: ['DELETE'])]
    public function delete(int $id, ClientRepository $clientRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $client = $clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($this->getUser()->getId() !== $client->getId()) {
            throw new AccessDeniedException('You do not have permission to delete this client.');
        }

        $entityManager->remove($client);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Client deleted successfully.'], JsonResponse::HTTP_NO_CONTENT);
    }
}
