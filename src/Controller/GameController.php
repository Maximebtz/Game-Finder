<?php

namespace App\Controller;

use App\Service\BoardGameGeekApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{

    private $boardGameGeekApiService;

    public function __construct(BoardGameGeekApiService $boardGameGeekApiService)
    {
        $this->boardGameGeekApiService = $boardGameGeekApiService;
    }

    // Show games list
    #[Route('/game', name: 'game_show')]
    public function index(): Response
    {

        $games = $this->boardGameGeekApiService->getGames();

        return $this->render('game/showGames.html.twig', [
            'controller_name' => 'GameController',
            'games' => $games
        ]);
    }

    // Show game details
    #[Route('/game/{id}', name: 'game_details')]
    public function show(string $id): Response
    {
        $game = $this->boardGameGeekApiService->getGameDetailsById($id);

        return $this->render('game/details.html.twig', [
            'controller_name' => 'GameController',
            'game' => $game
        ]);
    }
}
