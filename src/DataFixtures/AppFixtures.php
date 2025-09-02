<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Produit;
use App\Entity\Tva;
use App\Entity\Facture;
use App\Entity\ProFacture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Création des taux de TVA
        $tva20 = new Tva();
        $tva20->setTaux(20.000);
        $tva20->setDateDebutTva(new \DateTime('2022-01-01'));
        $manager->persist($tva20);

        $tva55 = new Tva();
        $tva55->setTaux(5.500);
        $tva55->setDateDebutTva(new \DateTime('2022-01-01'));
        $manager->persist($tva55);

        $tva7 = new Tva();
        $tva7->setTaux(7.000);
        $tva7->setDateDebutTva(new \DateTime('2022-01-01'));
        $manager->persist($tva7);

        // 2. Création des produits
        $produitA = new Produit();
        $produitA->setTarifsHTProduit(1500.00);
        $produitA->setTva($tva55);
        $manager->persist($produitA);

        $produitB = new Produit();
        $produitB->setTarifsHTProduit(4000.00);
        $produitB->setTva($tva7);
        $manager->persist($produitB);

        $produitC = new Produit();
        $produitC->setTarifsHTProduit(70000.00);
        $produitC->setTva($tva20);
        $manager->persist($produitC);

        $produitD = new Produit();
        $produitD->setTarifsHTProduit(3000.00);
        $produitD->setTva($tva20);
        $manager->persist($produitD);

        // 3. Création d'un client
        $client = new Client();
        $client->setClientNom('Mon client SAS');
        $client->setClientAdresse('45, rue du test 75016 PARIS');
        $manager->persist($client);

        $manager->flush();

        echo "✅ Fixtures créées avec succès !\n";
    }
}