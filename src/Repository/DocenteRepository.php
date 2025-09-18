<?php

namespace App\Repository;

use App\Entity\Docente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Docente>
 */
class DocenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Docente::class);
    }

    public function findByNombre(string $nombre): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.nombre LIKE :nombre OR d.apellido LIKE :nombre')
            ->setParameter('nombre', '%' . $nombre . '%')
            ->orderBy('d.apellido', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByEspecialidad(string $especialidad): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.especialidad LIKE :especialidad')
            ->setParameter('especialidad', '%' . $especialidad . '%')
            ->orderBy('d.apellido', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
