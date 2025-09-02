<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ProFactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProFactureRepository::class)]
class ProFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['facture:read'])]
    private ?int $quantite_produit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Groups(['facture:read'])]
    private ?string $tarifHT_produit = null; // Prix unitaire HT figé

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 3)]
    #[Groups(['facture:read'])]
    private ?string $taux_tva = null; // Taux de TVA figé (20%, 5.5%, etc.)

    #[ORM\Column(length: 255)]
    #[Groups(['facture:read'])]
    private ?string $designation = null; // Nom du produit figé

    #[ORM\ManyToOne(inversedBy: 'proFactures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $facture = null;

    #[ORM\ManyToOne(inversedBy: 'proFactures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    // NOTE: Suppression de la relation vers Tva
    // On ne garde plus qu'une référence au produit pour traçabilité,
    // mais les valeurs utilisées sont celles figées dans cette entité

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteProduit(): ?int
    {
        return $this->quantite_produit;
    }

    public function setQuantiteProduit(int $quantite_produit): static
    {
        $this->quantite_produit = $quantite_produit;

        return $this;
    }

    public function getTarifHTProduit(): ?string
    {
        return $this->tarifHT_produit;
    }

    public function setTarifHTProduit(string $tarifHT_produit): static
    {
        $this->tarifHT_produit = $tarifHT_produit;

        return $this;
    }

    public function getTauxTva(): ?string
    {
        return $this->taux_tva;
    }

    public function setTauxTva(string $taux_tva): static
    {
        $this->taux_tva = $taux_tva;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): static
    {
        $this->facture = $facture;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    // Méthodes de calcul (utiles pour l'affichage)
    public function getTotalHT(): string
    {
        return bcmul($this->tarifHT_produit, (string) $this->quantite_produit, 2);
    }

    public function getMontantTVA(): string
    {
        $totalHT = $this->getTotalHT();
        $tauxTVA = bcdiv($this->taux_tva, '100', 3); // Convertir 20% en 0.20
        return bcmul($totalHT, $tauxTVA, 2);
    }

    public function getTotalTTC(): string
    {
        $totalHT = $this->getTotalHT();
        $montantTVA = $this->getMontantTVA();
        return bcadd($totalHT, $montantTVA, 2);
    }
}