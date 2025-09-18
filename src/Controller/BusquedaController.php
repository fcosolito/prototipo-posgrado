<?php

namespace App\Controller;

use App\Repository\AlumnoRepository;
use App\Repository\CarreraRepository;
use App\Repository\CursoRepository;
use App\Repository\DocenteRepository;
use App\Repository\DictadoRepository;
use App\Repository\InscripcionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/busqueda')]
final class BusquedaController extends AbstractController
{
    #[Route('/', name: 'app_busqueda_index')]
    public function index(): Response
    {
        return $this->render('busqueda/index.html.twig');
    }

    #[Route('/avanzada', name: 'app_busqueda_avanzada', methods: ['GET', 'POST'])]
    public function avanzada(
        Request $request,
        AlumnoRepository $alumnoRepository,
        DocenteRepository $docenteRepository,
        CursoRepository $cursoRepository,
        CarreraRepository $carreraRepository
    ): Response {
        $resultados = [];
        $tipoBusqueda = $request->query->get('tipo', 'alumno');
        $termino = $request->query->get('termino', '');

        if ($termino) {
            switch ($tipoBusqueda) {
                case 'alumno':
                    $resultados = $alumnoRepository->createQueryBuilder('a')
                        ->where('a.nombre LIKE :termino OR a.apellido LIKE :termino OR a.dni LIKE :termino OR a.legajo LIKE :termino')
                        ->setParameter('termino', '%' . $termino . '%')
                        ->orderBy('a.apellido', 'ASC')
                        ->addOrderBy('a.nombre', 'ASC')
                        ->getQuery()
                        ->getResult();
                    break;

                case 'docente':
                    $resultados = $docenteRepository->createQueryBuilder('d')
                        ->where('d.nombre LIKE :termino OR d.apellido LIKE :termino OR d.especialidad LIKE :termino')
                        ->setParameter('termino', '%' . $termino . '%')
                        ->orderBy('d.apellido', 'ASC')
                        ->addOrderBy('d.nombre', 'ASC')
                        ->getQuery()
                        ->getResult();
                    break;

                case 'curso':
                    $resultados = $cursoRepository->createQueryBuilder('c')
                        ->leftJoin('c.carrera', 'car')
                        ->leftJoin('c.docente', 'd')
                        ->where('c.nombre LIKE :termino OR car.nombre LIKE :termino OR d.nombre LIKE :termino OR d.apellido LIKE :termino')
                        ->setParameter('termino', '%' . $termino . '%')
                        ->orderBy('c.nombre', 'ASC')
                        ->getQuery()
                        ->getResult();
                    break;

                case 'carrera':
                    $resultados = $carreraRepository->createQueryBuilder('c')
                        ->where('c.nombre LIKE :termino')
                        ->setParameter('termino', '%' . $termino . '%')
                        ->orderBy('c.nombre', 'ASC')
                        ->getQuery()
                        ->getResult();
                    break;
            }
        }

        return $this->render('busqueda/avanzada.html.twig', [
            'tipoBusqueda' => $tipoBusqueda,
            'termino' => $termino,
            'resultados' => $resultados,
        ]);
    }

    #[Route('/alumnos-por-carrera', name: 'app_busqueda_alumnos_carrera', methods: ['GET'])]
    public function alumnosPorCarrera(
        Request $request,
        CarreraRepository $carreraRepository,
        InscripcionRepository $inscripcionRepository
    ): Response {
        $carreraId = $request->query->get('carrera');
        $carrera = null;
        $alumnos = [];

        if ($carreraId) {
            $carrera = $carreraRepository->find($carreraId);
            if ($carrera) {
                $alumnos = $inscripcionRepository->createQueryBuilder('i')
                    ->join('i.dictado', 'd')
                    ->join('d.curso', 'c')
                    ->join('i.alumno', 'a')
                    ->where('c.carrera = :carrera')
                    ->setParameter('carrera', $carrera)
                    ->groupBy('a.id')
                    ->orderBy('a.apellido', 'ASC')
                    ->addOrderBy('a.nombre', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        }

        $carreras = $carreraRepository->findAll();

        return $this->render('busqueda/alumnos_carrera.html.twig', [
            'carreras' => $carreras,
            'carrera' => $carrera,
            'alumnos' => $alumnos,
            'carreraId' => $carreraId,
        ]);
    }

    #[Route('/cursos-por-docente', name: 'app_busqueda_cursos_docente', methods: ['GET'])]
    public function cursosPorDocente(
        Request $request,
        DocenteRepository $docenteRepository,
        CursoRepository $cursoRepository
    ): Response {
        $docenteId = $request->query->get('docente');
        $docente = null;
        $cursos = [];

        if ($docenteId) {
            $docente = $docenteRepository->find($docenteId);
            if ($docente) {
                $cursos = $cursoRepository->createQueryBuilder('c')
                    ->where('c.docente = :docente')
                    ->setParameter('docente', $docente)
                    ->orderBy('c.nombre', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        }

        $docentes = $docenteRepository->findAll();

        return $this->render('busqueda/cursos_docente.html.twig', [
            'docentes' => $docentes,
            'docente' => $docente,
            'cursos' => $cursos,
            'docenteId' => $docenteId,
        ]);
    }

    #[Route('/estadisticas-rapidas', name: 'app_busqueda_estadisticas_rapidas')]
    public function estadisticasRapidas(
        AlumnoRepository $alumnoRepository,
        DocenteRepository $docenteRepository,
        CursoRepository $cursoRepository,
        CarreraRepository $carreraRepository,
        InscripcionRepository $inscripcionRepository
    ): Response {
        $estadisticas = [
            'total_alumnos' => $alumnoRepository->count([]),
            'total_docentes' => $docenteRepository->count([]),
            'total_cursos' => $cursoRepository->count([]),
            'total_carreras' => $carreraRepository->count([]),
            'total_inscripciones' => $inscripcionRepository->count([]),
        ];

        return $this->render('busqueda/estadisticas_rapidas.html.twig', [
            'estadisticas' => $estadisticas,
        ]);
    }
}
