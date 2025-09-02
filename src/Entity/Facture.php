<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['facture:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['facture:read'])]
    private ?string $ref_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['facture:read'])]
    private ?\DateTime $date_facturation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['facture:read'])]
    private ?\DateTime $date_echeance = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Groups(['facture:read'])]
    private ?string $total_ht = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Groups(['facture:read'])]
    private ?string $total_ttc = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $conditions_reglement = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['facture:read'])]
    private ?Client $client = null;

    /**
     * @var Collection<int, ProFacture>
     */
    #[ORM\OneToMany(targetEntity: ProFacture::class, mappedBy: 'facture')]
    #[Groups(['facture:read'])]
    private Collection $proFactures;

    public function __construct()
    {
        $this->proFactures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefId(): ?string
    {
        return $this->ref_id;
    }

    public function setRefId(string $ref_id): static
    {
        $this->ref_id = $ref_id;

        return $this;
    }

    public function getDateFacturation(): ?\DateTime
    {
        return $this->date_facturation;
    }

    public function setDateFacturation(\DateTime $date_facturation): static
    {
        $this->date_facturation = $date_facturation;

        return $this;
    }

    public function getDateEcheance(): ?\DateTime
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(\DateTime $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }

    public function getTotalHt(): ?string
    {
        return $this->total_ht;
    }

    public function setTotalHt(string $total_ht): static
    {
        $this->total_ht = $total_ht;

        return $this;
    }

    public function getTotalTtc(): ?string
    {
        return $this->total_ttc;
    }

    public function setTotalTtc(string $total_ttc): static
    {
        $this->total_ttc = $total_ttc;

        return $this;
    }

    public function getConditionsReglement(): ?string
    {
        return $this->conditions_reglement;
    }

    public function setConditionsReglement(?string $conditions_reglement): static
    {
        $this->conditions_reglement = $conditions_reglement;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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
            $proFacture->setFacture($this);
        }

        return $this;
    }

    public function removeProFacture(ProFacture $proFacture): static
    {
        if ($this->proFactures->removeElement($proFacture)) {
            if ($proFacture->getFacture() === $this) {
                $proFacture->setFacture(null);
            }
        }

        return $this;
    }
}