<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\TvaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TvaRepository::class)]
class Tva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
  
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDebut_tva = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateFin_tva = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 3)]
    #[Groups(['facture:read'])]
    private ?string $taux = null;

    /**
     * @var Collection<int, ProFacture>
     */
    #[ORM\OneToMany(targetEntity: ProFacture::class, mappedBy: 'tva')]
    private Collection $proFactures;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'tva')]
    private Collection $produits;

    public function __construct()
    {
        $this->proFactures = new ArrayCollection();
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutTva(): ?\DateTime
    {
        return $this->dateDebut_tva;
    }

    public function setDateDebutTva(\DateTime $dateDebut_tva): static
    {
        $this->dateDebut_tva = $dateDebut_tva;

        return $this;
    }

    public function getDateFinTva(): ?\DateTime
    {
        return $this->dateFin_tva;
    }

    public function setDateFinTva(?\DateTime $dateFin_tva): static
    {
        $this->dateFin_tva = $dateFin_tva;

        return $this;
    }

    public function getTaux(): ?string
    {
        return $this->taux;
    }

    public function setTaux(string $taux): static
    {
        $this->taux = $taux;

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
            $proFacture->setTva($this);
        }

        return $this;
    }

    public function removeProFacture(ProFacture $proFacture): static
    {
        if ($this->proFactures->removeElement($proFacture)) {
            // set the owning side to null (unless already changed)
            if ($proFacture->getTva() === $this) {
                $proFacture->setTva(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setTva($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getTva() === $this) {
                $produit->setTva(null);
            }
        }

        return $this;
    }
}
