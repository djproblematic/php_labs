<?php

namespace App\Controller;

use App\Entity\RoomType;
use App\Form\RoomTypeFormType;
use App\Form\RoomFormType;
use App\Repository\RoomTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/room/type')]
final class RoomTypeController extends AbstractController{
    #[Route(name: 'app_room_type_index', methods: ['GET'])]
    public function index(RoomTypeRepository $roomTypeRepository): Response
    {
        return $this->render('room_type/index.html.twig', [
            'room_types' => $roomTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_room_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $roomType = new RoomType();
        $form = $this->createForm(RoomTypeFormType::class, $roomType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($roomType);
            $entityManager->flush();

            return $this->redirectToRoute('app_room_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room_type/new.html.twig', [
            'room_type' => $roomType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_room_type_show', methods: ['GET'])]
    public function show(RoomType $roomType): Response
    {
        return $this->render('room_type/show.html.twig', [
            'room_type' => $roomType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_room_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RoomType $roomType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomTypeFormType::class, $roomType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_room_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room_type/edit.html.twig', [
            'room_type' => $roomType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_room_type_delete', methods: ['POST'])]
    public function delete(Request $request, RoomType $roomType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$roomType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($roomType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_room_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
