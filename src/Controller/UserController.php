<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Service\BoardGameGeekApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{

    
    private $boardGameGeekApiService;

    public function __construct(BoardGameGeekApiService $boardGameGeekApiService)
    {
        $this->boardGameGeekApiService = $boardGameGeekApiService;
    }


    // Show user profile
    #[Route('/profile', name: 'profile')]
    public function profile(EventRepository $eventRepository): Response
    {
        $games = $this->boardGameGeekApiService->getGames();
        $eventOrganized = $eventRepository->findBy(['organizer' => $this->getUser()]);
        $eventPlayed = $eventRepository->findByParticipant($this->getUser());
        $organizedGamesList = [];
        $playedGamesList = [];
        
        foreach ($eventOrganized as $event) {
            $organizedGamesList[] = (string) $event->getGameId();
        }

        foreach ($eventPlayed as $event) {
            $playedGamesList[] = (string) $event->getGameId();
        }

        $organizedGamesList = $this->boardGameGeekApiService->getGamesByIds($organizedGamesList);
        $playedGamesList = $this->boardGameGeekApiService->getGamesByIds($playedGamesList);
        
        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
            'games' => $games,
            'eventOrganized' => $eventOrganized,
            'organizedGamesList' => $organizedGamesList,
            'playedGamesList' => $playedGamesList,
        ]);
    }

    // Edit user profile
    #[Route('/edit', name: 'edit')]
    public function edit(): Response
    {

        $user = $this->getUser();

        

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

}
