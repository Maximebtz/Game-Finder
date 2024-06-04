<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Follow;
use App\Enum\FollowStatus;
use App\Repository\UserRepository;
use App\Repository\FollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FollowController extends AbstractController
{
    // Follow a user
    #[Route('/follow/{id}', name: 'follow')]
    public function follow(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $follower = $this->getUser();

        $follow = new Follow();
        $follow->setFollower($follower);
        $follow->setFollowed($user);
        $follow->setStatus(FollowStatus::PENDING); // Set the follow status to PENDING

        $em->persist($follow);
        $em->flush();

        $this->addFlash('success', 'You have sent a follow request to ' . $user->getUsername());

        $referer = $request->headers->get('referer');

        if ($referer) {

            return new RedirectResponse($referer);
        } else {

            // Fournis un chemin de retour par défaut
            return $this->redirectToRoute('follow_list');
        }
    }

    // Accept a follow request
    #[Route('/accept/{id}', name: 'accept_follow')]
    public function acceptFollow(Request $request, Follow $follow, EntityManagerInterface $em): Response
    {
        $follow->setStatus(FollowStatus::ACCEPTED); // Set the follow status to ACCEPTED
        $em->flush();

        $this->addFlash('success', 'You have accepted the follow request from ' . $follow->getFollower()->getUsername());

        $referer = $request->headers->get('referer');

        if ($referer) {

            return new RedirectResponse($referer);
        } else {

            // Fournis un chemin de retour par défaut
            return $this->redirectToRoute('follow_list');
        }
    }

    // Reject a follow request
    #[Route('/reject/{id}', name: 'reject_follow')]
    public function rejectFollow(Request $request, Follow $follow, EntityManagerInterface $em): Response
    {
        $em->remove($follow);
        $em->flush();

        $this->addFlash('success', 'You have rejected the follow request from ' . $follow->getFollower()->getUsername());

        $referer = $request->headers->get('referer');

        if ($referer) {

            return new RedirectResponse($referer);
        } else {

            // Fournis un chemin de retour par défaut
            return $this->redirectToRoute('follow_list');
        }
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
    #[Route('/follow/list', name: 'follow_list')]
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

    // Add a friend page 
    #[Route('/friends', name: 'friends_list')]
    public function friends(Request $request, EntityManagerInterface $em, FollowRepository $followRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        $follows = $followRepository->findBy([
            'follower' => $user,
            'status' => FollowStatus::ACCEPTED,
        ]);

        $query = $request->query->get('query');
        $users = $userRepository->findByQuery($query);

        $followed = $followRepository->findBy([
            'followed' => $user,
            'status' => FollowStatus::ACCEPTED,
        ]);

        return $this->render('follow/list.html.twig', [
            'follows' => $follows,
            'users' => $users,
            'followed' => $followed,
        ]);
    }

    // get user json response for user search 
    #[Route('/user/search', name: 'user_search')]
    public function userSearch(Request $request, UserRepository $userRepository, FollowRepository $followRepository): Response
    {
        $query = $request->query->get('query');
        $users = $userRepository->findByQuery($query);
        $currentUser = $this->getUser();

        $response = [];
        foreach ($users as $user) {
            // Vérifie si l'utilisateur actuel suit déjà l'utilisateur trouvé
            $isFollowed = $followRepository->findOneBy([
                'follower' => $currentUser,
                'followed' => $user,
                'status' => FollowStatus::ACCEPTED,
            ]);

            $pendingFollow = $followRepository->findOneBy([
                'follower' => $currentUser,
                'followed' => $user,
                'status' => FollowStatus::PENDING,
            ]);

            $response[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'add_url' => $this->generateUrl('follow', ['id' => $user->getId()]),
                'is_followed' => $isFollowed ? true : false,
                'pending_follow' => $pendingFollow ? true : false,
            ];
        }

        return $this->json($response);
    }
}
