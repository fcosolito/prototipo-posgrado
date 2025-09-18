<?php

namespace App\Repository;

use App\Entity\Alumno;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alumno>
 */
class AlumnoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alumno::class);
    }

    public function search($criteria): array
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($criteria['nombre'])) {
            $qb->andWhere('a.nombre LIKE :nom')
                ->setParameter('nom', "%".$criteria['nombre']."%");
        }
        if (!empty($criteria['apellido'])) {
            $qb->andWhere('a.apellido LIKE :ap')
                ->setParameter('ap', "%".$criteria['apellido']."%");
        }
        if (!empty($criteria['dni'])) {
            $qb->andWhere('a.dni = :dni')
                ->setParameter('dni', $criteria['dni']);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Alumno[] Returns an array of Alumno objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Alumno
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
