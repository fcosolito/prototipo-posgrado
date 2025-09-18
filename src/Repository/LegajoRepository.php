<?php

namespace App\Repository;

use App\Entity\Legajo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Legajo>
 */
class LegajoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Legajo::class);
    }

    public function findByNumero(string $numero): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.numero LIKE :numero')
            ->setParameter('numero', '%' . $numero . '%')
            ->orderBy('l.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCarrera(int $carreraId): array
    {
        return $this->createQueryBuilder('l')
            ->join('l.carrera', 'c')
            ->where('c.id = :carreraId')
            ->setParameter('carreraId', $carreraId)
            ->orderBy('l.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByAlumno(int $alumnoId): array
    {
        return $this->createQueryBuilder('l')
            ->join('l.alumno', 'a')
            ->where('a.id = :alumnoId')
            ->setParameter('alumnoId', $alumnoId)
            ->orderBy('l.fechaInscripcion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByEstado(string $estado): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.estado = :estado')
            ->setParameter('estado', $estado)
            ->orderBy('l.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
