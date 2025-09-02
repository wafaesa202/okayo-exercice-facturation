<?php

namespace App\Repository;

use App\Entity\Tva;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @extends ServiceEntityRepository<Tva>
 */
class TvaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tva::class);
    }

    /**
     * Trouve le taux de TVA valide pour un produit à une date donnée
     *
     * @param int $produitId
     * @param \DateTimeInterface $date
     * @return string|null
     * @throws NonUniqueResultException
     */
    public function findTauxValidePourProduit(int $produitId, \DateTimeInterface $date): ?string
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.produits', 'p')
            ->where('p.id = :produitId')
            ->andWhere('t.dateDebut_tva <= :date')
            ->andWhere('t.dateFin_tva IS NULL OR t.dateFin_tva >= :date')
            ->setParameter('produitId', $produitId)
            ->setParameter('date', $date)
            ->orderBy('t.dateDebut_tva', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();
        
        return $result ? $result->getTaux() : null;
    }

    /**
     * Trouve l'entité Tva valide pour un produit à une date donnée
     * (Version alternative si vous avez besoin de l'entité complète)
     *
     * @param int $produitId
     * @param \DateTimeInterface $date
     * @return Tva|null
     * @throws NonUniqueResultException
     */
    public function findTvaValidePourProduit(int $produitId, \DateTimeInterface $date): ?Tva
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.produits', 'p')
            ->where('p.id = :produitId')
            ->andWhere('t.dateDebut_tva <= :date')
            ->andWhere('t.dateFin_tva IS NULL OR t.dateFin_tva >= :date')
            ->setParameter('produitId', $produitId)
            ->setParameter('date', $date)
            ->orderBy('t.dateDebut_tva', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    //    /**
    //     * @return Tva[] Returns an array of Tva objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tva
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}