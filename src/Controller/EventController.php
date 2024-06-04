<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Entity\Invitation;
use App\Enum\InvitationStatus;
use App\Repository\UserRepository;
use App\Service\BoardGameGeekApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{

    private $boardGameGeekApiService;

    public function __construct(BoardGameGeekApiService $boardGameGeekApiService)
    {
        $this->boardGameGeekApiService = $boardGameGeekApiService;
    }

    // Show event details
    #[Route('/{id}', name: 'show')]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/new', name: 'new')]
    public function new(string $id, Request $request, EntityManagerInterface $em)
    {
        $event = new Event();
        $event->setOrganizer($this->getUser());
        
        $id = $request->attributes->get('id');
        $game = $this->boardGameGeekApiService->getGameDetailsById($id);
        $event->setGameId($game['id']);
        $event->setTitle($game['name']);


        $form = $this->createForm(
            EventType::class,
            $event,
            ['user' => $this->getUser()]
        );
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();

            // Créer les invitations pour chaque participant
            foreach ($event->getParticipants() as $participant) {
                if ($participant !== $this->getUser()) {
                    $invitation = new Invitation();
                    $invitation->setEvent($event);
                    $invitation->setUser($participant);
                    $invitation->setStatus(InvitationStatus::PENDING);

                    $em->persist($invitation);
                }
            }


            $em->flush();

            $this->addFlash('success', 'Événement créé et invitations envoyées !');
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Edit an event
    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Event $event, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('EDIT', $event);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Événement modifié !');
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // delete an event
    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Event $event, EntityManagerInterface $em)
    {

        $em->remove($event);
        $em->flush();

        $this->addFlash('success', 'Événement supprimé !');
        return $this->redirectToRoute('home');
    }
}
