<?php

namespace App\Entity;

use App\Repository\DictadoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DictadoRepository::class)]
class Dictado
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaInicio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaFin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaInicio(): ?\DateTime
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTime $fechaInicio): static
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTime
    {
        return $this->fechaFin;
    }

    public function setFechaFin(\DateTime $fechaFin): static
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }
}
