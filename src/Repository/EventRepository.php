<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    // nombres d'évènements organisés par un utilisateur (organizer_id dans la table event)
    public function countEventsByUser(User $user): int
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.organizer = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // nombres d'évènements auxquels un utilisateur participe (user_id dans la table association event_user)
    public function countEventsByParticipant(User $user): int
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.invitations', 'i')
            ->where('i.user = :user')
            ->andWhere('i.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // liste des évènements auxquels un utilisateur participe (user_id dans la table association event_user)
    public function findByParticipant(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.invitations', 'i')
            ->where('i.user = :user')
            ->andWhere('i.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getResult();
    }
}
