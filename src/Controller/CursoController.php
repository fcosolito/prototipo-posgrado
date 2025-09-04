<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\Nota;
use App\Entity\Dictado;
use App\Entity\Inscripcion;
use App\Form\CursoType;
use App\Form\RegNotaCursoType;
use App\Repository\CursoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/curso')]
final class CursoController extends AbstractController
{
    #[Route(name: 'app_curso_index', methods: ['GET'])]
    public function index(CursoRepository $cursoRepository): Response
    {
        return $this->render('curso/index.html.twig', [
            'cursos' => $cursoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_curso_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $curso = new Curso();
        $form = $this->createForm(CursoType::class, $curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($curso);
            $entityManager->flush();

            return $this->redirectToRoute('app_curso_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('curso/new.html.twig', [
            'curso' => $curso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_curso_show', methods: ['GET'])]
    public function show(Curso $curso): Response
    {
        return $this->render('curso/show.html.twig', [
            'curso' => $curso,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_curso_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Curso $curso, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CursoType::class, $curso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_curso_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('curso/edit.html.twig', [
            'curso' => $curso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_curso_delete', methods: ['POST'])]
    public function delete(Request $request, Curso $curso, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$curso->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($curso);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_curso_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/notas', name: 'app_curso_notas', methods: ['GET', 'POST'])]
    public function registrar_nota(Curso $curso, Request $request, EntityManagerInterface $entityManager): Response
    {
        $inscripcionRepository = $entityManager->getRepository(Inscripcion::class);
        $notaRepository = $entityManager->getRepository(Nota::class);
        $dictadoRepository = $entityManager->getRepository(Dictado::class);

        $nota = new Nota();
        // Puede haber mas de un dictado vigente simultaneamente?
        // si es el caso se deberian mostrar los cursados vigentes 
        // y seleccionar uno
        $dictado = $dictadoRepository->findVigente($curso);
        $inscripciones = $inscripcionRepository->findBy(["dictado" => $dictado]);
        $notas = [];

        $form = $this->createForm(RegNotaCursoType::class, null, [
            "inscripciones" => $inscripciones,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!empty($data["alumno"]) && !empty($data["valor"])) {
                $insc = $inscripcionRepository->find($data["inscripcion"]);
                $nota->setInscripcion($insc);
                $nota->setValor($data["valor"]);

                $entityManager->persist($nota);
                $entityManager->flush();
            }
        }

        foreach ($inscripciones as $inscripcion) {
            $n = $notaRepository->findOneBy(["inscripcion" => $inscripcion]);
            if ($n) {
                $alumno = $n->getInscripcion()->getAlumno();
                $notas[] = [
                    "valor" => $n->getValor(),
                    "alumno" => $alumno->getNombre()." ".$alumno->getApellido()." (".$alumno->getDni().")",
                ];
            }
        }

        return $this->render('curso/notas.html.twig', [
            'curso' => $curso,
            'form' => $form,
            'notas' => $notas,
        ]);
    }
}
