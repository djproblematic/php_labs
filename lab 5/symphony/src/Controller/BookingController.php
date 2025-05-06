<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\ClientRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/bookings')]
class BookingController extends AbstractController
{
    #[Route('', name: 'api_booking_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ClientRepository $clientRepo,
        RoomRepository $roomRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['client_id'], $data['room_id'], $data['startDate'], $data['endDate'])) {
            return new JsonResponse(['error' => 'Missing data'], 400);
        }

        $client = $clientRepo->find($data['client_id']);
        $room = $roomRepo->find($data['room_id']);

        if (!$client || !$room) {
            return new JsonResponse(['error' => 'Invalid client or room'], 400);
        }

        $booking = new Booking();
        $booking->setClient($client);
        $booking->setRoom($room);
        $booking->setStartDate(new \DateTime($data['startDate']));
        $booking->setEndDate(new \DateTime($data['endDate']));

        $em->persist($booking);
        $em->flush();

        return new JsonResponse(['message' => 'Booking created successfully'], 201);
    }

    #[Route('', name: 'api_booking_list', methods: ['GET'])]
    public function list(BookingRepository $bookingRepo): JsonResponse
    {
        $bookings = $bookingRepo->findAll();
        $data = [];

        foreach ($bookings as $booking) {
            $data[] = [
                'id' => $booking->getId(),
                'client' => $booking->getClient()->getName(),
                'room' => $booking->getRoom()->getNumber(),
                'startDate' => $booking->getStartDate()->format('Y-m-d'),
                'endDate' => $booking->getEndDate()->format('Y-m-d'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'api_booking_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        BookingRepository $bookingRepo,
        ClientRepository $clientRepo,
        RoomRepository $roomRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $booking = $bookingRepo->find($id);
        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['client_id'])) {
            $client = $clientRepo->find($data['client_id']);
            if ($client) {
                $booking->setClient($client);
            }
        }

        if (isset($data['room_id'])) {
            $room = $roomRepo->find($data['room_id']);
            if ($room) {
                $booking->setRoom($room);
            }
        }

        if (isset($data['startDate'])) {
            $booking->setStartDate(new \DateTime($data['startDate']));
        }

        if (isset($data['endDate'])) {
            $booking->setEndDate(new \DateTime($data['endDate']));
        }

        $em->flush();

        return new JsonResponse(['message' => 'Booking updated successfully']);
    }

    #[Route('/{id}', name: 'api_booking_delete', methods: ['DELETE'])]
    public function delete(int $id, BookingRepository $bookingRepo, EntityManagerInterface $em): JsonResponse
    {
        $booking = $bookingRepo->find($id);
        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        $em->remove($booking);
        $em->flush();

        return new JsonResponse(['message' => 'Booking deleted successfully']);
    }
}
