<?php

namespace App\Controller;

use App\Entity\Carrera;
use App\Entity\Curso;
use App\Entity\Nota;
use App\Entity\Dictado;
use App\Entity\Inscripcion;
use App\Form\CursoType;
use App\Form\RegDictadoCursoType;
use App\Form\RegNotaCursoType;
use App\Form\SelDictadoCursoType;
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
        $carreras = $entityManager->getRepository(Carrera::class)->findAll();

        $curso = new Curso();
        $form = $this->createForm(CursoType::class, $curso, [
            "carreras" => $carreras,
        ]);
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
        $carreras = $entityManager->getRepository(Carrera::class)->findAll();

        $form = $this->createForm(CursoType::class, $curso, [
            "carreras" => $carreras,
        ]);
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

    #[Route('/{id}/dictados', name: 'app_curso_dictados', methods: ['GET', 'POST'])]
    public function registrar_dictado(Curso $curso, Request $request, EntityManagerInterface $entityManager): Response
    {
        $dictadoRepository = $entityManager->getRepository(Dictado::class);

        $form = $this->createForm(RegDictadoCursoType::class, null, [
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            if (!empty($data["fechaInicio"])) {
                $fechaFin = !empty($data["fechaFin"]) ? $data["fechaFin"] : null;
                $nombre = !empty($data["nombre"]) ? $data["nombre"] : null;

                $dictado = new Dictado();

                $dictado->setCurso($curso);
                $dictado->setFechaInicio($data["fechaInicio"]);
                $dictado->setFechaFin($fechaFin);
                $dictado->setNombre($nombre);

                $entityManager->persist($dictado);
                $entityManager->flush();
            }
        }

        $dictados = $dictadoRepository->findBy(["curso" => $curso]);
        $dictados_ser = [];

        foreach ($dictados as $dic) {
            $inicio = $dic->getFechaInicio()->format("d-m-Y");
            $fin = $dic->getFechaFin()->format("d-m-Y");

            $dictados_ser[] = [
                "fechaInicio" => $inicio,
                "fechaFin" => $fin,
                "id" => $dic->getId(),
                "nombre" => $dic->getNombre(),
            ];
        }

        return $this->render('curso/dictados.html.twig', [
            'curso' => $curso,
            'form' => $form,
            'dictados' => $dictados_ser,
        ]);
    }
}
