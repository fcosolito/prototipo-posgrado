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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_index')]
    public function index(
        AlumnoRepository $alumnoRepository,
        CarreraRepository $carreraRepository,
        CursoRepository $cursoRepository,
        DictadoRepository $dictadoRepository,
        InscripcionRepository $inscripcionRepository,
        NotaRepository $notaRepository,
        PagoRepository $pagoRepository,
        CuotaRepository $cuotaRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Estadísticas generales
        $totalAlumnos = $alumnoRepository->count([]);
        $totalCarreras = $carreraRepository->count([]);
        $totalCursos = $cursoRepository->count([]);
        $totalDictados = $dictadoRepository->count([]);
        
        // Estadísticas de inscripciones
        $totalInscripciones = $inscripcionRepository->count([]);
        $inscripcionesActivas = $inscripcionRepository->createQueryBuilder('i')
            ->join('i.dictado', 'd')
            ->where('d.fechaFin >= :hoy')
            ->setParameter('hoy', new \DateTime())
            ->getQuery()
            ->getResult();
        
        // Estadísticas de pagos
        $totalCuotas = $cuotaRepository->count([]);
        $cuotasPagadas = $pagoRepository->count([]);
        $cuotasPendientes = $totalCuotas - $cuotasPagadas;
        
        // Recaudación total
        $recaudacionTotal = $pagoRepository->createQueryBuilder('p')
            ->select('SUM(p.valor)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
        // Recaudación del mes actual
        $recaudacionMesActual = $pagoRepository->createQueryBuilder('p')
            ->select('SUM(p.valor)')
            ->join('p.cuota', 'c')
            ->where('MONTH(c.fechaVencimiento) = :mes')
            ->andWhere('YEAR(c.fechaVencimiento) = :año')
            ->setParameter('mes', (new \DateTime())->format('n'))
            ->setParameter('año', (new \DateTime())->format('Y'))
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
        // Top 5 carreras por inscripciones
        $topCarreras = $entityManager->createQuery(
            'SELECT c.nombre, COUNT(i.id) as inscripciones 
             FROM App\Entity\Carrera c 
             JOIN c.cursos co 
             JOIN co.dictados d 
             JOIN d.inscripciones i 
             GROUP BY c.id 
             ORDER BY inscripciones DESC'
        )->setMaxResults(5)->getResult();
        
        // Alumnos con más cuotas pendientes
        $alumnosConDeuda = $entityManager->createQuery(
            'SELECT a.nombre, a.apellido, COUNT(c.id) as cuotasPendientes, SUM(c.valor) as deudaTotal
             FROM App\Entity\Alumno a 
             LEFT JOIN a.cuotas c 
             LEFT JOIN c.pago p 
             WHERE p.id IS NULL 
             GROUP BY a.id 
             HAVING cuotasPendientes > 0
             ORDER BY deudaTotal DESC'
        )->setMaxResults(10)->getResult();
        
        // Dictados próximos a finalizar
        $dictadosProximos = $dictadoRepository->createQueryBuilder('d')
            ->where('d.fechaFin BETWEEN :hoy AND :proximoMes')
            ->setParameter('hoy', new \DateTime())
            ->setParameter('proximoMes', (new \DateTime())->modify('+1 month'))
            ->orderBy('d.fechaFin', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
        
        // Promedio de notas por carrera
        $promedioNotasPorCarrera = $entityManager->createQuery(
            'SELECT c.nombre, AVG(n.valor) as promedio 
             FROM App\Entity\Carrera c 
             JOIN c.cursos co 
             JOIN co.dictados d 
             JOIN d.inscripciones i 
             JOIN i.nota n 
             GROUP BY c.id 
             ORDER BY promedio DESC'
        )->getResult();
        
        return $this->render('dashboard/index.html.twig', [
            'totalAlumnos' => $totalAlumnos,
            'totalCarreras' => $totalCarreras,
            'totalCursos' => $totalCursos,
            'totalDictados' => $totalDictados,
            'totalInscripciones' => $totalInscripciones,
            'inscripcionesActivas' => count($inscripcionesActivas),
            'totalCuotas' => $totalCuotas,
            'cuotasPagadas' => $cuotasPagadas,
            'cuotasPendientes' => $cuotasPendientes,
            'recaudacionTotal' => $recaudacionTotal,
            'recaudacionMesActual' => $recaudacionMesActual,
            'topCarreras' => $topCarreras,
            'alumnosConDeuda' => $alumnosConDeuda,
            'dictadosProximos' => $dictadosProximos,
            'promedioNotasPorCarrera' => $promedioNotasPorCarrera,
        ]);
    }
}
