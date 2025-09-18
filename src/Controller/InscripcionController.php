<?php

namespace App\Controller;

use App\Entity\Inscripcion;
use App\Entity\Dictado;
use App\Entity\Alumno;
use App\Form\InscripcionType;
use App\Repository\InscripcionRepository;
use App\Repository\DictadoRepository;
use App\Repository\AlumnoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inscripcion')]
final class InscripcionController extends AbstractController
{
    #[Route('/', name: 'app_inscripcion_index', methods: ['GET'])]
    public function index(InscripcionRepository $inscripcionRepository): Response
    {
        return $this->render('inscripcion/index.html.twig', [
            'inscripciones' => $inscripcionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inscripcion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inscripcion = new Inscripcion();
        $form = $this->createForm(InscripcionType::class, $inscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verificar si ya existe una inscripción para este alumno en este dictado
            $existeInscripcion = $entityManager->getRepository(Inscripcion::class)
                ->findOneBy([
                    'alumno' => $inscripcion->getAlumno(),
                    'dictado' => $inscripcion->getDictado()
                ]);

            if ($existeInscripcion) {
                $this->addFlash('error', 'El alumno ya está inscrito en este dictado.');
                return $this->render('inscripcion/new.html.twig', [
                    'inscripcion' => $inscripcion,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($inscripcion);
            $entityManager->flush();

            $this->addFlash('success', 'Inscripción realizada exitosamente.');
            return $this->redirectToRoute('app_inscripcion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscripcion/new.html.twig', [
            'inscripcion' => $inscripcion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inscripcion_show', methods: ['GET'])]
    public function show(Inscripcion $inscripcion): Response
    {
        return $this->render('inscripcion/show.html.twig', [
            'inscripcion' => $inscripcion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inscripcion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscripcion $inscripcion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscripcionType::class, $inscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Inscripción actualizada exitosamente.');
            return $this->redirectToRoute('app_inscripcion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscripcion/edit.html.twig', [
            'inscripcion' => $inscripcion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inscripcion_delete', methods: ['POST'])]
    public function delete(Request $request, Inscripcion $inscripcion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscripcion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inscripcion);
            $entityManager->flush();
            $this->addFlash('success', 'Inscripción eliminada exitosamente.');
        }

        return $this->redirectToRoute('app_inscripcion_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/dictado/{id}', name: 'app_inscripcion_por_dictado', methods: ['GET'])]
    public function inscripcionesPorDictado(Dictado $dictado, InscripcionRepository $inscripcionRepository): Response
    {
        $inscripciones = $inscripcionRepository->createQueryBuilder('i')
            ->where('i.dictado = :dictado')
            ->setParameter('dictado', $dictado)
            ->orderBy('i.alumno', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('inscripcion/por_dictado.html.twig', [
            'dictado' => $dictado,
            'inscripciones' => $inscripciones,
        ]);
    }

    #[Route('/alumno/{id}', name: 'app_inscripcion_por_alumno', methods: ['GET'])]
    public function inscripcionesPorAlumno(Alumno $alumno, InscripcionRepository $inscripcionRepository): Response
    {
        $inscripciones = $inscripcionRepository->createQueryBuilder('i')
            ->where('i.alumno = :alumno')
            ->setParameter('alumno', $alumno)
            ->orderBy('i.dictado', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('inscripcion/por_alumno.html.twig', [
            'alumno' => $alumno,
            'inscripciones' => $inscripciones,
        ]);
    }
}
