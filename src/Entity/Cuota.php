<?php

namespace App\Entity;

use App\Repository\CuotaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CuotaRepository::class)]
class Cuota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaVencimiento = null;

    #[ORM\Column]
    private ?float $valor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaVencimiento(): ?\DateTime
    {
        return $this->fechaVencimiento;
    }

    public function setFechaVencimiento(\DateTime $fechaVencimiento): static
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): static
    {
        $this->valor = $valor;

        return $this;
    }
}
