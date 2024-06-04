<?php

namespace App\Entity;

use App\Enum\FollowStatus;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FollowRepository;

#[ORM\Entity(repositoryClass: FollowRepository::class)]
class Follow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    private ?User $follower = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    private ?User $followed = null;

    #[ORM\Column(type: 'string', length: 50, options: ['default' => FollowStatus::PENDING], enumType:FollowStatus::class)]
    private ?FollowStatus $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollower(): ?User
    {
        return $this->follower;
    }

    public function setFollower(?User $follower): static
    {
        $this->follower = $follower;

        return $this;
    }

    public function getFollowed(): ?User
    {
        return $this->followed;
    }

    public function setFollowed(?User $followed): static
    {
        $this->followed = $followed;

        return $this;
    }

   
    public function getStatus(): FollowStatus
    {
        return $this->status;
    }

    public function setStatus(FollowStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
