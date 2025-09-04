<?php

namespace App\Repository;

use App\Entity\Curso;
use App\Entity\Dictado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dictado>
 */
class DictadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dictado::class);
    }

    // el dictado vigente asociado al curso
    // ahora solo busca un dictado cualquiera de $curso
    public function findVigente(Curso $curso) : ?Dictado {
        return $this->createQueryBuilder('d')
            ->andWhere('d.curso = :val')
            ->setParameter('val', $curso->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return Dictado[] Returns an array of Dictado objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Dictado
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
