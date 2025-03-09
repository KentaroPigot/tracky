<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use App\Enum\TicketType;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: 'is_granted("ROLE_USER")', normalizationContext: ['groups' => ['ticket:item:read']]),
        new Post(security: 'is_granted("ROLE_USER")'),
        new GetCollection(security: 'is_granted("ROLE_USER")', normalizationContext: ['groups' => ['ticket:collection:read']]),
        new Put(security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_PROJECT_MANAGER") or (is_granted("ROLE_USER") and object.getSubmitter() == user)'),
        new Patch(security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_PROJECT_MANAGER") or (is_granted("ROLE_USER") and object.getSubmitter() == user)'),
        new Delete(security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_PROJECT_MANAGER")')
    ],
    normalizationContext: ['groups' => ['ticket:read']],
    denormalizationContext: ['groups' => ['ticket:write']]
)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['ticket:item:read', 'ticket:item:read', 'ticket:write', 'project:item:read'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['ticket:item:read', 'ticket:write'])]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Groups(['ticket:item:read', 'project:item:read', 'ticket:write'])]
    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?User $assignedDeveloper = null;

    #[Groups(['ticket:item:read', 'project:item:read', 'ticket:write',])]
    #[ORM\ManyToOne(inversedBy: 'submittedTickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $submitter = null;

    #[Groups(['ticket:item:read', 'ticket:write'])]
    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[Groups(['ticket:item:read', 'ticket:write'])]
    #[ORM\Column(enumType: TicketPriority::class)]
    private ?TicketPriority $priority = null;

    #[Groups(['ticket:item:read', 'project:item:read', 'ticket:write'])]
    #[ORM\Column(enumType: TicketStatus::class)]
    private ?TicketStatus $status = null;

    #[Groups(['ticket:item:read', 'ticket:write'])]
    #[ORM\Column(enumType: TicketType::class)]
    private ?TicketType $type = null;

    #[Groups(['ticket:item:read', 'project:item:read'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAssignedDeveloper(): ?User
    {
        return $this->assignedDeveloper;
    }

    public function setAssignedDeveloper(?User $assignedDeveloper): static
    {
        $this->assignedDeveloper = $assignedDeveloper;

        return $this;
    }

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): static
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getPriority(): ?TicketPriority
    {
        return $this->priority;
    }

    public function setPriority(TicketPriority $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): ?TicketStatus
    {
        return $this->status;
    }

    public function setStatus(TicketStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?TicketType
    {
        return $this->type;
    }

    public function setType(TicketType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
