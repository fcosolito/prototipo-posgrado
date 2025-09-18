<?php

namespace App\Controller;

use App\Repository\AlumnoRepository;
use App\Repository\CarreraRepository;
use App\Repository\CursoRepository;
use App\Repository\DictadoRepository;
use App\Repository\InscripcionRepository;
use App\Repository\NotaRepository;
use App\Repository\PagoRepository;
use App\Repository\CuotaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reportes')]
final class ReporteController extends AbstractController
{
    #[Route('/', name: 'app_reporte_index')]
    public function index(): Response
    {
        return $this->render('reporte/index.html.twig');
    }

    #[Route('/notas-curso', name: 'app_reporte_notas_curso')]
    public function notasCurso(
        Request $request,
        CursoRepository $cursoRepository,
        NotaRepository $notaRepository
    ): Response {
        $cursoId = $request->query->get('curso');
        $curso = null;
        $notas = [];

        if ($cursoId) {
            $curso = $cursoRepository->find($cursoId);
            if ($curso) {
                $notas = $notaRepository->createQueryBuilder('n')
                    ->join('n.inscripcion', 'i')
                    ->join('i.dictado', 'd')
                    ->join('i.alumno', 'a')
                    ->where('d.curso = :curso')
                    ->setParameter('curso', $curso)
                    ->orderBy('a.apellido', 'ASC')
                    ->addOrderBy('a.nombre', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        }

        $cursos = $cursoRepository->findAll();

        return $this->render('reporte/notas_curso.html.twig', [
            'cursos' => $cursos,
            'curso' => $curso,
            'notas' => $notas,
            'cursoId' => $cursoId,
        ]);
    }

    #[Route('/notas-alumno', name: 'app_reporte_notas_alumno')]
    public function notasAlumno(
        Request $request,
        AlumnoRepository $alumnoRepository,
        NotaRepository $notaRepository
    ): Response {
        $alumnoId = $request->query->get('alumno');
        $alumno = null;
        $notas = [];

        if ($alumnoId) {
            $alumno = $alumnoRepository->find($alumnoId);
            if ($alumno) {
                $notas = $notaRepository->createQueryBuilder('n')
                    ->join('n.inscripcion', 'i')
                    ->join('i.dictado', 'd')
                    ->join('d.curso', 'c')
                    ->where('i.alumno = :alumno')
                    ->setParameter('alumno', $alumno)
                    ->orderBy('c.nombre', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        }

        $alumnos = $alumnoRepository->findAll();

        return $this->render('reporte/notas_alumno.html.twig', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'notas' => $notas,
            'alumnoId' => $alumnoId,
        ]);
    }

    #[Route('/estado-cuotas', name: 'app_reporte_estado_cuotas')]
    public function estadoCuotas(
        Request $request,
        AlumnoRepository $alumnoRepository,
        CuotaRepository $cuotaRepository,
        PagoRepository $pagoRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $alumnoId = $request->query->get('alumno');
        $alumno = null;
        $cuotas = [];

        if ($alumnoId) {
            $alumno = $alumnoRepository->find($alumnoId);
            if ($alumno) {
                $cuotas = $cuotaRepository->createQueryBuilder('c')
                    ->leftJoin('c.pago', 'p')
                    ->where('c.alumno = :alumno')
                    ->setParameter('alumno', $alumno)
                    ->orderBy('c.fechaVencimiento', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        }

        // Resumen de estado de cuotas por alumno
        $resumenCuotas = $entityManager->createQuery(
            'SELECT a.nombre, a.apellido, a.legajo,
                    COUNT(c.id) as totalCuotas,
                    COUNT(p.id) as cuotasPagadas,
                    (COUNT(c.id) - COUNT(p.id)) as cuotasPendientes,
                    COALESCE(SUM(CASE WHEN p.id IS NULL THEN c.valor ELSE 0 END), 0) as deudaTotal
             FROM App\Entity\Alumno a 
             LEFT JOIN a.cuotas c 
             LEFT JOIN c.pago p 
             GROUP BY a.id 
             HAVING totalCuotas > 0
             ORDER BY deudaTotal DESC'
        )->getResult();

        $alumnos = $alumnoRepository->findAll();

        return $this->render('reporte/estado_cuotas.html.twig', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'cuotas' => $cuotas,
            'resumenCuotas' => $resumenCuotas,
            'alumnoId' => $alumnoId,
        ]);
    }

    #[Route('/exportar-notas-carrera', name: 'app_reporte_exportar_notas_carrera')]
    public function exportarNotasCarrera(
        Request $request,
        AlumnoRepository $alumnoRepository,
        CarreraRepository $carreraRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $alumnoId = $request->query->get('alumno');
        $alumno = null;
        $notasPorCarrera = [];

        if ($alumnoId) {
            $alumno = $alumnoRepository->find($alumnoId);
            if ($alumno) {
                // Obtener notas agrupadas por carrera
                $notasPorCarrera = $entityManager->createQuery(
                    'SELECT c.nombre as carrera, co.nombre as curso, n.valor as nota, d.nombre as dictado
                     FROM App\Entity\Carrera c 
                     JOIN c.cursos co 
                     JOIN co.dictados d 
                     JOIN d.inscripciones i 
                     JOIN i.nota n 
                     WHERE i.alumno = :alumno
                     ORDER BY c.nombre, co.nombre'
                )->setParameter('alumno', $alumno)->getResult();
            }
        }

        $alumnos = $alumnoRepository->findAll();

        return $this->render('reporte/exportar_notas_carrera.html.twig', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'notasPorCarrera' => $notasPorCarrera,
            'alumnoId' => $alumnoId,
        ]);
    }

    #[Route('/estadisticas-generales', name: 'app_reporte_estadisticas_generales')]
    public function estadisticasGenerales(
        AlumnoRepository $alumnoRepository,
        CarreraRepository $carreraRepository,
        CursoRepository $cursoRepository,
        NotaRepository $notaRepository,
        PagoRepository $pagoRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Estadísticas generales
        $totalAlumnos = $alumnoRepository->count([]);
        $totalCarreras = $carreraRepository->count([]);
        $totalCursos = $cursoRepository->count([]);
        $totalNotas = $notaRepository->count([]);
        $totalPagos = $pagoRepository->count([]);

        // Promedio de notas por carrera
        $promedioNotasPorCarrera = $entityManager->createQuery(
            'SELECT c.nombre, AVG(n.valor) as promedio, COUNT(n.id) as cantidadNotas
             FROM App\Entity\Carrera c 
             JOIN c.cursos co 
             JOIN co.dictados d 
             JOIN d.inscripciones i 
             JOIN i.nota n 
             GROUP BY c.id 
             ORDER BY promedio DESC'
        )->getResult();

        // Distribución de notas
        $distribucionNotas = $entityManager->createQuery(
            'SELECT n.valor as nota, COUNT(n.id) as cantidad
             FROM App\Entity\Nota n 
             GROUP BY n.valor 
             ORDER BY n.valor'
        )->getResult();

        // Recaudación por mes (últimos 12 meses)
        $recaudacionPorMes = $entityManager->createQuery(
            'SELECT MONTH(c.fechaVencimiento) as mes, YEAR(c.fechaVencimiento) as año, SUM(p.valor) as total
             FROM App\Entity\Cuota c 
             JOIN c.pago p 
             WHERE c.fechaVencimiento >= :fechaInicio
             GROUP BY YEAR(c.fechaVencimiento), MONTH(c.fechaVencimiento)
             ORDER BY año, mes'
        )->setParameter('fechaInicio', (new \DateTime())->modify('-12 months'))->getResult();

        return $this->render('reporte/estadisticas_generales.html.twig', [
            'totalAlumnos' => $totalAlumnos,
            'totalCarreras' => $totalCarreras,
            'totalCursos' => $totalCursos,
            'totalNotas' => $totalNotas,
            'totalPagos' => $totalPagos,
            'promedioNotasPorCarrera' => $promedioNotasPorCarrera,
            'distribucionNotas' => $distribucionNotas,
            'recaudacionPorMes' => $recaudacionPorMes,
        ]);
    }
}
