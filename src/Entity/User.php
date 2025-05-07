<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $inscriptionDate = null;

    #[ORM\Column(length : 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'demander')]
    private Collection $usersDemande;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'usersDemande')]
    #[ORM\JoinTable(name: 'user_demande',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'demander_id', referencedColumnName: 'id')]
    )]
    private Collection $demander;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'userAccepte')]
    #[ORM\JoinTable(name: 'user_accepter',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'accepter_id', referencedColumnName: 'id')]
    )]
    private Collection $accepter;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'accepter')]
    private Collection $userAccepte;
    

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Fichier::class)]
    private Collection $fichiers;

    /**
     * @var Collection<int, Fichier>
     */
    #[ORM\ManyToMany(targetEntity: Fichier::class, mappedBy: 'AccesAmis')]
    private Collection $fichiersAmis;

    public function __construct()
    {
        $this->fichiers = new ArrayCollection();
        $this->demander = new ArrayCollection();
        $this->usersDemande = new ArrayCollection();
        $this->accepter = new ArrayCollection();
        $this->userAccepte = new ArrayCollection();
        $this->fichiersAmis = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getInscriptionDate(): ?\DateTimeInterface
    {
        return $this->inscriptionDate;
    }

    public function setInscriptionDate(\DateTimeInterface $inscriptionDate): static
    {
        $this->inscriptionDate = $inscriptionDate;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichier $fichier): static
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers->add($fichier);
            $fichier->setUser($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): static
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getUser() === $this) {
                $fichier->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsersDemande(): Collection
    {
        return $this->usersDemande;
    }

    public function addUsersDemande(self $usersDemande): static
    {
        if (!$this->usersDemande->contains($usersDemande)) {
            $this->usersDemande->add($usersDemande);
            $usersDemande->addDemander($this);
        }

        return $this;
    }

    public function removeUsersDemande(self $usersDemande): static
    {
        if ($this->usersDemande->removeElement($usersDemande)) {
            $usersDemande->removeDemander($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAccepter(): Collection
    {
        return $this->accepter;
    }

    public function addAccepter(self $accepter): static
    {
        if (!$this->accepter->contains($accepter)) {
            $this->accepter->add($accepter);
        }

        return $this;
    }

    public function removeAccepter(self $accepter): static
    {
        $this->accepter->removeElement($accepter);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getDemander(): Collection
    {
        return $this->demander;
    }

    public function addDemander(self $demander): static
    {
        if (!$this->demander->contains($demander)) {
            $this->demander->add($demander);
        }

        return $this;
    }

    public function removeDemander(self $demander): static
    {
        $this->demander->removeElement($demander);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUserAccepte(): Collection
    {
        return $this->userAccepte;
        
    }

    public function addUserAccepte(self $userAccepte): static
    {
        if (!$this->userAccepte->contains($userAccepte)) {
            $this->userAccepte->add($userAccepte);
            $userAccepte->addAccepter($this);
        }

        return $this;
    }

    public function removeUserAccepte(self $userAccepte): static
    {
        if ($this->userAccepte->removeElement($userAccepte)) {
            $userAccepte->removeAccepter($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getFichiersAmis(): Collection
    {
        return $this->fichiersAmis;
    }

    public function addFichiersAmi(Fichier $fichiersAmi): static
    {
        if (!$this->fichiersAmis->contains($fichiersAmi)) {
            $this->fichiersAmis->add($fichiersAmi);
            $fichiersAmi->addAccesAmi($this);
        }

        return $this;
    }

    public function removeFichiersAmi(Fichier $fichiersAmi): static
    {
        if ($this->fichiersAmis->removeElement($fichiersAmi)) {
            $fichiersAmi->removeAccesAmi($this);
        }

        return $this;
    }

}
