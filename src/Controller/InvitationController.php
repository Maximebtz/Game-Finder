<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Enum\InvitationStatus;
use App\Repository\InvitationRepository;
use App\Service\BoardGameGeekApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/invitation', name: 'invitation_')]
class InvitationController extends AbstractController
{

    private $boardGameGeekApiService;

    public function __construct(BoardGameGeekApiService $boardGameGeekApiService)
    {
        $this->boardGameGeekApiService = $boardGameGeekApiService;
    }
    
    #[Route('/{id}/accept', name: 'accept')]
    public function accept(Invitation $invitation, EntityManagerInterface $em)
    {
        if (!$invitation->getEvent()) {
            $this->addFlash('danger', 'L\'événement a été supprimé.');
            return $this->redirectToRoute('home');
        }

        $invitation->setStatus(InvitationStatus::ACCEPTED);
        $em->flush();

        $this->addFlash('success', 'Invitation acceptée!');
        return $this->redirectToRoute('event_show', ['id' => $invitation->getEvent()->getId()]);
    }


    #[Route('/{id}/refuse', name: 'refuse')]
    public function refuse(Invitation $invitation, EntityManagerInterface $em)
    {
        if (!$invitation->getEvent()) {
            $this->addFlash('danger', 'L\'événement a été supprimé.');
            return $this->redirectToRoute('home');
        }

        $invitation->setStatus(InvitationStatus::REFUSED);
        $em->flush();

        $this->addFlash('success', 'Invitation refusée.');
        return $this->redirectToRoute('event_show', ['id' => $invitation->getEvent()->getId()]);
    }

    // Show invitation
    #[Route('/{id}/game_{gameId}', name: 'show')]
    public function show(Invitation $invitation, string $gameId, $id)
    {
        $gameId = (string) $invitation->getEvent()->getGameId();
        $game = $this->boardGameGeekApiService->getGameDetailsById($gameId);
      
        

        return $this->render('invitation/show.html.twig', [
            'invitation' => $invitation,
            'game' => $game
        ]);
    }


    public function headerSearch(InvitationRepository $invitationRepository): Response
    {
        // $user = $this->getUser();
        // dd($user);
        $pendingInvitations = $invitationRepository->findBy(['user' => $this->getUser(), 'status' => 'PENDING']);
        
        return $this->render('components/header/_header-search.html.twig', [
            'pendingInvitations' => $pendingInvitations
        ]);
    }
}
