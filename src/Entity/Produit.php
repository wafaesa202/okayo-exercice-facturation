<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['facture:read'])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Groups(['facture:read'])]
    private ?string $tarifsHT_produit = null;

    /**
     * @var Collection<int, ProFacture>
     */
    #[ORM\OneToMany(targetEntity: ProFacture::class, mappedBy: 'produit')]
    private Collection $proFactures;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tva $tva = null;

    public function __construct()
    {
        $this->proFactures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTarifsHTProduit(): ?string
    {
        return $this->tarifsHT_produit;
    }

    public function setTarifsHTProduit(string $tarifsHT_produit): static
    {
        $this->tarifsHT_produit = $tarifsHT_produit;

        return $this;
    }

    /**
     * @return Collection<int, ProFacture>
     */
    public function getProFactures(): Collection
    {
        return $this->proFactures;
    }

    public function addProFacture(ProFacture $proFacture): static
    {
        if (!$this->proFactures->contains($proFacture)) {
            $this->proFactures->add($proFacture);
            $proFacture->setProduit($this);
        }

        return $this;
    }

    public function removeProFacture(ProFacture $proFacture): static
    {
        if ($this->proFactures->removeElement($proFacture)) {
            // set the owning side to null (unless already changed)
            if ($proFacture->getProduit() === $this) {
                $proFacture->setProduit(null);
            }
        }

        return $this;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): static
    {
        $this->tva = $tva;

        return $this;
    }
}