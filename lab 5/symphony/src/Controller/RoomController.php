<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomType; 
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/room')]
final class RoomController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route(name: 'api_room_index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findAll();

        return new JsonResponse(
            $this->serializer->serialize($rooms, 'json', ['groups' => 'room:read']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_room_show', methods: ['GET'])]
    public function show(int $id, RoomRepository $roomRepository): JsonResponse
    {
        $room = $roomRepository->find($id);
        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $this->serializer->serialize($room, 'json', ['groups' => 'room:read']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/new', name: 'api_room_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['number']) || !isset($data['capacity']) || !isset($data['price']) || !isset($data['roomType'])) {
            return new JsonResponse(['error' => 'Missing required fields: number, capacity, price, or roomType'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $room = new Room();
        $room->setNumber($data['number']);
        $room->setCapacity($data['capacity']);
        $room->setPrice($data['price']);
    
        $roomType = $entityManager->getRepository(RoomType::class)->find($data['roomType']);
        if ($roomType) {
            $room->setRoomType($roomType);
        }
    
        $entityManager->persist($room);
        $entityManager->flush();
    
        return new JsonResponse(
            $serializer->serialize($room, 'json', ['groups' => 'room:read']),
            JsonResponse::HTTP_CREATED,
            [],
            true
        );
    }
    

}
