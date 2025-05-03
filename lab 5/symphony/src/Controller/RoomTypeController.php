<?php

namespace App\Controller;

use App\Entity\RoomType;
use App\Form\RoomTypeFormType;
use App\Repository\RoomTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/room/type')]
final class RoomTypeController extends AbstractController
{
    #[Route(name: 'app_room_type_index', methods: ['GET'])]
    public function index(
        Request $request,
        RoomTypeRepository $roomTypeRepository,
        PaginatorInterface $paginator
    ): Response {
        $queryBuilder = $roomTypeRepository->createQueryBuilder('r');

        if ($request->query->get('id')) {
            $queryBuilder->andWhere('r.id = :id')
                         ->setParameter('id', $request->query->get('id'));
        }

        if ($request->query->get('name')) {
            $queryBuilder->andWhere('r.name LIKE :name')
                         ->setParameter('name', '%' . $request->query->get('name') . '%');
        }

        if ($request->query->get('description')) {
            $queryBuilder->andWhere('r.description LIKE :description')
                         ->setParameter('description', '%' . $request->query->get('description') . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            max(1, $request->query->getInt('itemsPerPage', 10))
        );

        return $this->render('room_type/index.html.twig', [
            'room_types' => $pagination,
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
