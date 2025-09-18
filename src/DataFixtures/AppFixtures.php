<?php

namespace App\DataFixtures;

use App\Entity\Alumno;
use App\Entity\Carrera;
use App\Entity\Curso;
use App\Entity\Dictado;
use App\Entity\Docente;
use App\Entity\Inscripcion;
use App\Entity\Nota;
use App\Entity\Cuota;
use App\Entity\Pago;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Crear carreras
        $carreras = [];
        $carreraNombres = [
            'Maestría en Ingeniería de Software',
            'Especialización en Inteligencia Artificial',
            'Doctorado en Ciencias de la Computación',
            'Maestría en Gestión de Proyectos'
        ];

        foreach ($carreraNombres as $nombre) {
            $carrera = new Carrera();
            $carrera->setNombre($nombre);
            $manager->persist($carrera);
            $carreras[] = $carrera;
        }

        // Crear docentes
        $docentes = [];
        $docenteData = [
            ['Dr. Juan', 'Pérez', 'juan.perez@universidad.edu', 12345678, 'Ingeniería de Software', 'Doctor en Ciencias de la Computación'],
            ['Dra. María', 'González', 'maria.gonzalez@universidad.edu', 23456789, 'Inteligencia Artificial', 'Doctora en Informática'],
            ['Dr. Carlos', 'Rodríguez', 'carlos.rodriguez@universidad.edu', 34567890, 'Bases de Datos', 'Doctor en Ingeniería'],
            ['Mg. Ana', 'Martín', 'ana.martin@universidad.edu', 45678901, 'Gestión de Proyectos', 'Magíster en Administración']
        ];

        foreach ($docenteData as $data) {
            $docente = new Docente();
            $docente->setNombre($data[0]);
            $docente->setApellido($data[1]);
            $docente->setCorreo($data[2]);
            $docente->setDni($data[3]);
            $docente->setEspecialidad($data[4]);
            $docente->setTitulo($data[5]);
            $manager->persist($docente);
            $docentes[] = $docente;
        }

        // Crear cursos
        $cursos = [];
        $cursoData = [
            ['Desarrollo de Software', 60, $carreras[0], $docentes[0], true, 15000],
            ['Machine Learning', 40, $carreras[1], $docentes[1], true, 18000],
            ['Bases de Datos Avanzadas', 30, $carreras[0], $docentes[2], true, 12000],
            ['Gestión de Proyectos Ágiles', 45, $carreras[3], $docentes[3], true, 16000],
            ['Seminario de Investigación', 20, $carreras[2], $docentes[0], false, 8000]
        ];

        foreach ($cursoData as $data) {
            $curso = new Curso();
            $curso->setNombre($data[0]);
            $curso->setHoras($data[1]);
            $curso->setCarrera($data[2]);
            $curso->setDocente($data[3]);
            $curso->setEsObligatorio($data[4]);
            $curso->setTarifaMensual($data[5]);
            $manager->persist($curso);
            $cursos[] = $curso;
        }

        // Crear alumnos
        $alumnos = [];
        $alumnoData = [
            ['María', 'López', 'maria.lopez@email.com', 11111111, 20111111111, 'ML001'],
            ['Juan', 'García', 'juan.garcia@email.com', 22222222, 20222222222, 'JG002'],
            ['Ana', 'Martínez', 'ana.martinez@email.com', 33333333, 20333333333, 'AM003'],
            ['Carlos', 'Fernández', 'carlos.fernandez@email.com', 44444444, 20444444444, 'CF004'],
            ['Laura', 'Sánchez', 'laura.sanchez@email.com', 55555555, 20555555555, 'LS005'],
            ['Diego', 'Romero', 'diego.romero@email.com', 66666666, 20666666666, 'DR006']
        ];

        foreach ($alumnoData as $data) {
            $alumno = new Alumno();
            $alumno->setNombre($data[0]);
            $alumno->setApellido($data[1]);
            $alumno->setCorreo($data[2]);
            $alumno->setDni($data[3]);
            $alumno->setCuil($data[4]);
            $alumno->setLegajo($data[5]);
            $manager->persist($alumno);
            $alumnos[] = $alumno;
        }

        // Crear dictados
        $dictados = [];
        foreach ($cursos as $index => $curso) {
            $dictado = new Dictado();
            $dictado->setCurso($curso);
            $dictado->setFechaInicio(new DateTime('2024-03-01'));
            $dictado->setFechaFin(new DateTime('2024-06-30'));
            $dictado->setNombre('Dictado 2024-1 - ' . $curso->getNombre());
            $manager->persist($dictado);
            $dictados[] = $dictado;
        }

        // Crear inscripciones y notas
        $inscripciones = [];
        foreach ($dictados as $dictado) {
            foreach (array_slice($alumnos, 0, rand(3, 6)) as $alumno) {
                $inscripcion = new Inscripcion();
                $inscripcion->setAlumno($alumno);
                $inscripcion->setDictado($dictado);
                $manager->persist($inscripcion);
                $inscripciones[] = $inscripcion;

                // Crear nota aleatoria
                $nota = new Nota();
                $nota->setInscripcion($inscripcion);
                $nota->setValor(rand(4, 10)); // Notas entre 4 y 10
                $manager->persist($nota);
            }
        }

        // Crear cuotas y pagos
        foreach ($alumnos as $index => $alumno) {
            for ($mes = 1; $mes <= 6; $mes++) {
                $cuota = new Cuota();
                $cuota->setAlumno($alumno);
                $cuota->setFechaVencimiento(new DateTime("2024-{$mes}-15"));
                $cuota->setValor(rand(10000, 20000));
                $manager->persist($cuota);

                // Algunas cuotas están pagadas (70% de probabilidad)
                if (rand(1, 10) <= 7) {
                    $pago = new Pago();
                    $pago->setCuota($cuota);
                    $pago->setValor($cuota->getValor());
                    $manager->persist($pago);
                }
            }
        }

        $manager->flush();
    }
}
