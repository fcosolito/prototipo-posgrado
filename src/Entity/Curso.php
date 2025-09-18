<?php

namespace App\Entity;

use App\Repository\CursoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursoRepository::class)]
class Curso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?int $horas = null;

    #[ORM\ManyToOne]
    private ?Carrera $carrera = null;

    #[ORM\ManyToOne(inversedBy: 'cursos')]
    private ?Docente $docente = null;

    #[ORM\Column(nullable: true)]
    private ?bool $esObligatorio = true;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $tarifaMensual = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getHoras(): ?int
    {
        return $this->horas;
    }

    public function setHoras(int $horas): static
    {
        $this->horas = $horas;

        return $this;
    }

    public function getCarrera(): ?Carrera
    {
        return $this->carrera;
    }

    public function setCarrera(?Carrera $carrera): static
    {
        $this->carrera = $carrera;

        return $this;
    }

    public function getDocente(): ?Docente
    {
        return $this->docente;
    }

    public function setDocente(?Docente $docente): static
    {
        $this->docente = $docente;

        return $this;
    }

    public function isEsObligatorio(): ?bool
    {
        return $this->esObligatorio;
    }

    public function setEsObligatorio(?bool $esObligatorio): static
    {
        $this->esObligatorio = $esObligatorio;

        return $this;
    }

    public function getTarifaMensual(): ?string
    {
        return $this->tarifaMensual;
    }

    public function setTarifaMensual(?string $tarifaMensual): static
    {
        $this->tarifaMensual = $tarifaMensual;

        return $this;
    }
}
