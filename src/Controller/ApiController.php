<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    #[Route('/api/game-finder/allEvents', name: 'api_events', methods: ['GET'])]
    public function getAllEvents(EventRepository $eventRepository): JsonResponse
    {
        // Fetching all events
        $events = $eventRepository->findAll();

        // Return the events as JSON
        return $this->json($events, JsonResponse::HTTP_OK, [], ['groups' => 'event:read']);
    }
}
