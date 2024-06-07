<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Entity\Invitation;
use App\Enum\InvitationStatus;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Service\BoardGameGeekApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
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
    #[Route('/{id}/detail', name: 'show')]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em, string $id): Response
    {
        $event = new Event();
        $event->setOrganizer($this->getUser());

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
    public function edit(Event $event, Request $request, EntityManagerInterface $em): Response
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

    // Delete an event
    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Event $event, EntityManagerInterface $em): Response
    {
        $em->remove($event);
        $em->flush();

        $this->addFlash('success', 'Événement supprimé !');
        return $this->redirectToRoute('home');
    }

    // List of events
    #[Route('/list', name: 'list')]
    public function list(EventRepository $eventRepository): Response
    {
        $user = $this->getUser();

        // Récupération des événements pour l'utilisateur actuel
        $playedEvents = $eventRepository->findByParticipant($user);
        $organizedEvents = $eventRepository->findByOrganizer($user);
        $pastEvents = $eventRepository->findPastEvents();
        $invitedEvents = $eventRepository->findByInvited($user);

        // Récupérer les jeux pour les différents types d'événements
        $organizedGamesList = $this->getGamesForEvents($organizedEvents);
        $playedGamesList = $this->getGamesForEvents($playedEvents);
        $pastGamesList = $this->getGamesForEvents($pastEvents);
        $invitedGamesList = $this->getGamesForEvents($invitedEvents);

        return $this->render('event/list.html.twig', [
            'playedEvents' => $playedEvents,
            'organizedEvents' => $organizedEvents,
            'pastEvents' => $pastEvents,
            'invitedEvents' => $invitedEvents,
            'organizedGamesList' => $organizedGamesList,
            'playedGamesList' => $playedGamesList,
            'pastGamesList' => $pastGamesList,
            'invitedGamesList' => $invitedGamesList,
        ]);
    }

    private function getGamesForEvents(array $events): array
    {
        $gameIds = array_map(fn ($event) => $event->getGameId(), $events);
        return $this->boardGameGeekApiService->getGamesByIds($gameIds);
    }
}
