<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichierRepository::class)]
class Fichier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $nomOriginal = null;

    #[ORM\Column(length: 255)]
    private ?string $nomServeur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\Column(length : 5)]
    private ?string $extension = null;

    #[ORM\Column]
    private ?int $taille = null;

    /**
     * @var Collection<int, Scategorie>
     */
    #[ORM\ManyToMany(targetEntity: Scategorie::class, inversedBy: 'fichiers')]
    private Collection $scategories;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'fichiersAmis')]
    private Collection $AccesAmis;

    public function __construct()
    {
        $this->scategories = new ArrayCollection();
        $this->AccesAmis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getNomOriginal(): ?string
    {
        return $this->nomOriginal;
    }

    public function setNomOriginal(string $nomOriginal): static
    {
        $this->nomOriginal = $nomOriginal;

        return $this;
    }

    public function getNomServeur(): ?string
    {
        return $this->nomServeur;
    }

    public function setNomServeur(string $nomServeur): static
    {
        $this->nomServeur = $nomServeur;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(int $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * @return Collection<int, Scategorie>
     */
    public function getScategories(): Collection
    {
        return $this->scategories;
    }

    public function addScategory(Scategorie $scategory): static
    {
        if (!$this->scategories->contains($scategory)) {
            $this->scategories->add($scategory);
        }

        return $this;
    }

    public function removeScategory(Scategorie $scategory): static
    {
        $this->scategories->removeElement($scategory);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAccesAmis(): Collection
    {
        return $this->AccesAmis;
    }

    public function addAccesAmi(User $accesAmi): static
    {
        if (!$this->AccesAmis->contains($accesAmi)) {
            $this->AccesAmis->add($accesAmi);
        }

        return $this;
    }

    public function removeAccesAmi(User $accesAmi): static
    {
        $this->AccesAmis->removeElement($accesAmi);

        return $this;
    }
}
