<?php

namespace App\Controller;

use App\Entity\Pago;
use App\Entity\Cuota;
use App\Form\PagoType;
use App\Repository\PagoRepository;
use App\Repository\CuotaRepository;
use App\Repository\AlumnoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pago')]
final class PagoController extends AbstractController
{
    #[Route('/', name: 'app_pago_index', methods: ['GET'])]
    public function index(PagoRepository $pagoRepository): Response
    {
        return $this->render('pago/index.html.twig', [
            'pagos' => $pagoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pago_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pago = new Pago();
        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pago);
            $entityManager->flush();

            $this->addFlash('success', 'Pago registrado exitosamente.');
            return $this->redirectToRoute('app_pago_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pago/new.html.twig', [
            'pago' => $pago,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pago_show', methods: ['GET'])]
    public function show(Pago $pago): Response
    {
        return $this->render('pago/show.html.twig', [
            'pago' => $pago,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pago_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pago $pago, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Pago actualizado exitosamente.');
            return $this->redirectToRoute('app_pago_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pago/edit.html.twig', [
            'pago' => $pago,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pago_delete', methods: ['POST'])]
    public function delete(Request $request, Pago $pago, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pago->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pago);
            $entityManager->flush();
            $this->addFlash('success', 'Pago eliminado exitosamente.');
        }

        return $this->redirectToRoute('app_pago_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cuotas-pendientes', name: 'app_pago_cuotas_pendientes', methods: ['GET'])]
    public function cuotasPendientes(CuotaRepository $cuotaRepository): Response
    {
        $cuotasPendientes = $cuotaRepository->createQueryBuilder('c')
            ->leftJoin('c.pago', 'p')
            ->where('p.id IS NULL')
            ->orderBy('c.fechaVencimiento', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('pago/cuotas_pendientes.html.twig', [
            'cuotasPendientes' => $cuotasPendientes,
        ]);
    }

    #[Route('/alumno/{id}', name: 'app_pago_por_alumno', methods: ['GET'])]
    public function pagosPorAlumno(Alumno $alumno, CuotaRepository $cuotaRepository): Response
    {
        $cuotas = $cuotaRepository->createQueryBuilder('c')
            ->leftJoin('c.pago', 'p')
            ->where('c.alumno = :alumno')
            ->setParameter('alumno', $alumno)
            ->orderBy('c.fechaVencimiento', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('pago/por_alumno.html.twig', [
            'alumno' => $alumno,
            'cuotas' => $cuotas,
        ]);
    }

    #[Route('/registrar-pago/{cuotaId}', name: 'app_pago_registrar_rapido', methods: ['GET', 'POST'])]
    public function registrarPagoRapido(
        int $cuotaId,
        Request $request,
        CuotaRepository $cuotaRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $cuota = $cuotaRepository->find($cuotaId);
        
        if (!$cuota) {
            $this->addFlash('error', 'Cuota no encontrada.');
            return $this->redirectToRoute('app_pago_cuotas_pendientes');
        }

        if ($cuota->getPago()) {
            $this->addFlash('error', 'Esta cuota ya está pagada.');
            return $this->redirectToRoute('app_pago_cuotas_pendientes');
        }

        if ($request->isMethod('POST')) {
            $valorPago = $request->request->get('valor');
            
            if ($valorPago && $valorPago > 0) {
                $pago = new Pago();
                $pago->setCuota($cuota);
                $pago->setValor($valorPago);
                
                $entityManager->persist($pago);
                $entityManager->flush();
                
                $this->addFlash('success', 'Pago registrado exitosamente.');
                return $this->redirectToRoute('app_pago_cuotas_pendientes');
            } else {
                $this->addFlash('error', 'El valor del pago debe ser mayor a 0.');
            }
        }

        return $this->render('pago/registrar_rapido.html.twig', [
            'cuota' => $cuota,
        ]);
    }
}
