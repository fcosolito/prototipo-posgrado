<?php

namespace App\DataFixtures;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Entity\Dictado;
use App\Entity\Inscripcion;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $alumno = new Alumno();
        $alumno->setNombre("Franco");
        $alumno->setApellido("Cosolito");
        $alumno->setDni(43423333);
        $alumno->setCorreo("cosolitofh@gmail.com");
        $alumno->setCuil(3222222222);

        $manager->persist($alumno);

        $curso = new Curso();
        $curso->setNombre("Curso 1");
        $curso->setHoras(30);

        $manager->persist($curso);

        $dictado = new Dictado();
        $dictado->setCurso($curso);
        $dictado->setFechaInicio(new DateTime());
        $dictado->setFechaFin(new DateTime());

        $manager->persist($dictado);

        $inscripcion = new Inscripcion();
        $inscripcion->setAlumno($alumno);
        $inscripcion->setDictado($dictado);

        $manager->persist($inscripcion);

        $manager->flush();
    }
}
