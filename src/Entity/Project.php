<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['project:item:read']]),
        new Post(security: 'is_granted("ROLE_PROJECT_MANAGER") or is_granted("ROLE_ADMIN")'),
        new GetCollection(normalizationContext: ['groups' => ['project:collection:read']]),
        new Put(security: 'is_granted("ROLE_PROJECT_MANAGER", "ROLE_ADMIN")'),
        new Patch(security: 'is_granted("ROLE_PROJECT_MANAGER", "ROLE_ADMIN")'),
        new Delete(security: 'is_granted("ROLE_PROJECT_MANAGER", "ROLE_ADMIN")')
    ],
    normalizationContext: ['groups' => ['project:read']],
    denormalizationContext: ['groups' => ['project:write']]
)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['project:item:read', 'project:collection:read', 'project:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:item:read', 'project:collection:read', 'project:write'])]
    private ?string $description = null;

    /**
     * @var Collection<int, User>
     */
    #[Groups(['project:item:read', 'project:write'])]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projects')]
    private Collection $assignedPersonnels;

    /**
     * @var Collection<int, Ticket>
     */
    #[Groups(['project:item:read', 'ticket:read'])]
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'project', orphanRemoval: true)]
    private Collection $tickets;

    #[Groups(['project:item:read'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->assignedPersonnels = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAssignedPersonnels(): Collection
    {
        return $this->assignedPersonnels;
    }

    public function addAssignedPersonnel(User $assignedPersonnel): static
    {
        if (!$this->assignedPersonnels->contains($assignedPersonnel)) {
            $this->assignedPersonnels->add($assignedPersonnel);
        }

        return $this;
    }

    public function removeAssignedPersonnel(User $assignedPersonnel): static
    {
        $this->assignedPersonnels->removeElement($assignedPersonnel);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setProject($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getProject() === $this) {
                $ticket->setProject(null);
            }
        }

        return $this;
    }
}
