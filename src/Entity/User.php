<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\State\UserPasswordHasher;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['user::item:read']]),
        new Post(
            validationContext: ['groups' => ['user:create']],
            denormalizationContext: ['groups' => ['user:write']],
            processor: UserPasswordHasher::class
        ),
        new GetCollection(),
        new Put(
            security: "is_granted('ROLE_USER') and object == user",
            denormalizationContext: ['groups' => ['user:update']],
            processor: UserPasswordHasher::class
        ),
        new Patch(
            security: "is_granted('ROLE_USER') and object == user",
            denormalizationContext: ['groups' => ['user:update']],
            processor: UserPasswordHasher::class
        ),
        new Delete(security: 'is_granted("ROLE_ADMIN")')
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write', "project:item:read", "ticket:item:read",])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write', "project:item:read", "ticket:item:read",])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Groups(['admin:write', 'project:item:read'])]
    private array $roles = [];

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write', 'project:item:read'])]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Groups(['user:write', 'user:update'])]
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\Length(min: 6, groups: ['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Url]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'assignedPersonnels')]
    #[Groups(['user:projects'])]
    private Collection $projects;

    /**
     * @var Collection<int, Ticket>
     */
    #[Groups(['user:tickets'])]
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'assignedDeveloper')]
    private Collection $tickets;

    /**
     * @var Collection<int, Ticket>
     */
    #[Groups(['user:tickets'])]
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'submitter')]
    private Collection $submittedTickets;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->projects = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->submittedTickets = new ArrayCollection();
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getRoles(): array
    {
        $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addAssignedPersonnel($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeAssignedPersonnel($this);
        }

        return $this;
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
            $ticket->setAssignedDeveloper($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getAssignedDeveloper() === $this) {
                $ticket->setAssignedDeveloper(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getSubmittedTickets(): Collection
    {
        return $this->submittedTickets;
    }

    public function addSubmittedTicket(Ticket $submittedTicket): static
    {
        if (!$this->submittedTickets->contains($submittedTicket)) {
            $this->submittedTickets->add($submittedTicket);
            $submittedTicket->setSubmitter($this);
        }

        return $this;
    }

    public function removeSubmittedTicket(Ticket $submittedTicket): static
    {
        if ($this->submittedTickets->removeElement($submittedTicket)) {
            // set the owning side to null (unless already changed)
            if ($submittedTicket->getSubmitter() === $this) {
                $submittedTicket->setSubmitter(null);
            }
        }

        return $this;
    }
}
