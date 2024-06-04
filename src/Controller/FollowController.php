<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Follow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Enum\FollowStatus;
use App\Repository\FollowRepository;

class FollowController extends AbstractController
{
    // Follow a user
    #[Route('/follow/{id}', name: 'follow')]
    public function follow(User $user, EntityManagerInterface $em): Response
    {
        $follower = $this->getUser();

        $follow = new Follow();
        $follow->setFollower($follower);
        $follow->setFollowed($user);
        $follow->setStatus(FollowStatus::PENDING); // Set the follow status to PENDING

        $em->persist($follow);
        $em->flush();

        $this->addFlash('success', 'You have sent a follow request to ' . $user->getUsername());

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    // Accept a follow request
    #[Route('/accept/{id}', name: 'accept_follow')]
    public function acceptFollow(Follow $follow, EntityManagerInterface $em): Response
    {
        $follow->setStatus(FollowStatus::ACCEPTED); // Set the follow status to ACCEPTED
        $em->flush();

        $this->addFlash('success', 'You have accepted the follow request from ' . $follow->getFollower()->getUsername());

        return $this->redirectToRoute('user_show', ['id' => $follow->getFollower()->getId()]);
    }

    // Reject a follow request
    #[Route('/reject/{id}', name: 'reject_follow')]
    public function rejectFollow(Follow $follow, EntityManagerInterface $em): Response
    {
        $em->remove($follow);
        $em->flush();

        $this->addFlash('success', 'You have rejected the follow request from ' . $follow->getFollower()->getUsername());

        return $this->redirectToRoute('user_show', ['id' => $follow->getFollower()->getId()]);
    }

    // Unfollow a user
    #[Route('/unfollow/{id}', name: 'unfollow')]
    public function unfollow(User $user, EntityManagerInterface $em, FollowRepository $followRepository): Response
    {
        $follower = $this->getUser();

        $follow = $followRepository->findOneBy([
            'follower' => $follower,
            'followed' => $user,
        ]);

        if ($follow) {
            $em->remove($follow);
            $em->flush();

            $this->addFlash('success', 'You are no longer following ' . $user->getUsername());
        }

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    // follow list
    public function followList(User $user, EntityManagerInterface $em, FollowRepository $followRepository): Response
    {
        $follows = $followRepository->findBy([
            'follower' => $user,
            'status' => FollowStatus::ACCEPTED,
        ]);
        
        $avatar = $user->getAvatar();

        return $this->render('components/friends/_friends-bar.html.twig', [
            'follows' => $follows,
        ]);
    }
}
