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

    #[ORM\OneToOne(mappedBy: 'cuota', cascade: ['persist', 'remove'])]
    private ?Pago $pago = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumno $alumno = null;

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

    public function getPago(): ?Pago
    {
        return $this->pago;
    }

    public function setPago(Pago $pago): static
    {
        // set the owning side of the relation if necessary
        if ($pago->getCuota() !== $this) {
            $pago->setCuota($this);
        }

        $this->pago = $pago;

        return $this;
    }

    public function getAlumno(): ?Alumno
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumno $alumno): static
    {
        $this->alumno = $alumno;

        return $this;
    }
}
