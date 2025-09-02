<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\ProFacture;
use App\Repository\FactureRepository;
use App\Repository\ProduitRepository;
use App\Repository\TvaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/factures')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository, SerializerInterface $serializer): JsonResponse
    {
        $factures = $factureRepository->findAll();
        
        return new JsonResponse(
            $serializer->serialize($factures, 'json', ['groups' => 'facture:read']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($facture, 'json', ['groups' => 'facture:read']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/', name: 'app_facture_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
        TvaRepository $tvaRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation des données requises
        if (!isset($data['reference'], $data['dateFacturation'], $data['dateEcheance'], $data['clientId'], $data['lignes'])) {
            return new JsonResponse(
                ['error' => 'Données manquantes: reference, dateFacturation, dateEcheance, clientId, lignes sont obligatoires'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Création de la facture
        $facture = new Facture();
        $facture->setRefId($data['reference']);
        $facture->setDateFacturation(new \DateTime($data['dateFacturation']));
        $facture->setDateEcheance(new \DateTime($data['dateEcheance']));
        $facture->setClient($entityManager->getReference(\App\Entity\Client::class, $data['clientId']));
        $facture->setConditionsReglement($data['conditionsReglement'] ?? 'Règlement à la livraison');
        
        $totalHt = '0';
        $totalTtc = '0';

        // Ajout des lignes de facture
        foreach ($data['lignes'] as $ligneData) {
            if (!isset($ligneData['produitId'], $ligneData['quantite'])) {
                continue; // ou retourner une erreur
            }

            $produit = $produitRepository->find($ligneData['produitId']);
            if (!$produit) {
                return new JsonResponse(
                    ['error' => "Produit non trouvé pour l'ID: " . $ligneData['produitId']],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            // Récupérer le taux de TVA valide à la date de facturation
            $tauxTVA = $tvaRepository->findTauxValidePourProduit(
                $produit->getId(),
                $facture->getDateFacturation()
            );

            if (!$tauxTVA) {
                return new JsonResponse(
                    ['error' => "Aucun taux de TVA valide trouvé pour le produit ID: " . $produit->getId() . " à la date " . $facture->getDateFacturation()->format('Y-m-d')],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            // Utiliser le prix unitaire fourni ou le prix courant du produit
            $prixUnitaireHt = $ligneData['prixUnitaireHt'] ?? $produit->getTarifsHTProduit();

            $ligne = new ProFacture();
            $ligne->setFacture($facture);
            $ligne->setProduit($produit); // Pour la traçabilité
            $ligne->setDesignation($produit->getNom()); // Nom figé
            $ligne->setQuantiteProduit($ligneData['quantite']);
            $ligne->setTarifHTProduit($prixUnitaireHt); // Prix figé
            $ligne->setTauxTva($tauxTVA); // Taux TVA figé

            // Calcul des totaux avec bcmath pour la précision
            $totalLigneHt = bcmul($prixUnitaireHt, (string) $ligneData['quantite'], 2);
            $tauxTvaDecimal = bcdiv($tauxTVA, '100', 5); // Convertir 20% en 0.20
            $totalLigneTva = bcmul($totalLigneHt, $tauxTvaDecimal, 2);
            $totalLigneTtc = bcadd($totalLigneHt, $totalLigneTva, 2);

            $totalHt = bcadd($totalHt, $totalLigneHt, 2);
            $totalTtc = bcadd($totalTtc, $totalLigneTtc, 2);

            $entityManager->persist($ligne);
        }

        $facture->setTotalHt($totalHt);
        $facture->setTotalTtc($totalTtc);

        $entityManager->persist($facture);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($facture, 'json', ['groups' => 'facture:read']),
            JsonResponse::HTTP_CREATED,
            [],
            true
        );
    }
}