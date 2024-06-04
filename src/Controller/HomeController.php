<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Service\BoardGameGeekApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    private $boardGameGeekApiService;

    public function __construct(BoardGameGeekApiService $boardGameGeekApiService)
    {
        $this->boardGameGeekApiService = $boardGameGeekApiService;
    }


    #[Route('/home', name: 'app_home')]
    public function index(EventRepository $eventRepository, InvitationRepository $invitationRepository): Response
    {
        $games = $this->boardGameGeekApiService->getGames();
        $nbrOfEventsOrganizedByUser = $eventRepository->countEventsByUser($this->getUser());
        $nbrOfEventsPlayedByUser = $eventRepository->countEventsByParticipant($this->getUser());

        // $pendingInvitations = $invitationRepository->findBy(['user' => $this->getUser(), 'status' => 'PENDING']);
        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'games' => $games,
            'nbrOfEventsOrganizedByUser' => $nbrOfEventsOrganizedByUser,
            'nbrOfEventsPlayedByUser' => $nbrOfEventsPlayedByUser,
            // 'pendingInvitations' => $pendingInvitations
        ]);
    }
}
