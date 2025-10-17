<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function findEmpruntsBetweenDates(\DateTime $debut, \DateTime $fin): array
{
    return $this->createQueryBuilder('e')
        ->join('e.idLivre', 'l')
        ->join('e.idUtilisateur', 'u')
        ->where('e.DateEmprunt BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->orderBy('e.DateEmprunt', 'ASC')
        ->getQuery()
        ->getResult();
}

    /**
     * Retourne les emprunts dont le livre appartient à un auteur donné entre deux dates.
     *
     * @param \DateTime $debut
     * @param \DateTime $fin
     * @param int $auteurId
     * @return Emprunt[]
     */
    public function findEmpruntsBetweenDatesByAuthor(\DateTime $debut, \DateTime $fin, int $auteurId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.idLivre', 'l')
            ->join('l.idAuteur', 'a')
            ->join('e.idUtilisateur', 'u')
            ->where('e.DateEmprunt BETWEEN :debut AND :fin')
            ->andWhere('a.id = :auteurId')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->setParameter('auteurId', $auteurId)
            ->orderBy('e.DateEmprunt', 'ASC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Emprunt[] Returns an array of Emprunt objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Emprunt
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
