<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Entity\Curso;
use App\Entity\Dictado;
use App\Entity\Inscripcion;
use App\Entity\Nota;
use App\Form\AlumnoSearchType;
use App\Form\AlumnoType;
use App\Form\InscribirAlumnoCursoType;
use App\Form\InscribirAlumnoDictadoType;
use App\Form\InscripcionCursoType;
use App\Form\RegNotaAlumnoType;
use App\Repository\AlumnoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/alumno')]
final class AlumnoController extends AbstractController
{
    #[Route(name: 'app_alumno_index', methods: ['GET', 'POST'])]
    public function index(Request $request, AlumnoRepository $alumnoRepository): Response
    {
        $form = $this->createForm(AlumnoSearchType::class);
        $form->handleRequest($request);

        $criteria = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!empty($data['nombre'])) {
                $criteria['nombre'] = $data['nombre'];
            }
            if (!empty($data['apellido'])) {
                $criteria['apellido'] = $data['apellido'];
            }
            if (!empty($data['dni'])) {
                $criteria['dni'] = $data['dni'];
            }
        }

        $alumnos = $alumnoRepository->search($criteria);

        return $this->render('alumno/index.html.twig', [
            'alumnos' => $alumnos,
            'form' => $form
        ]);
    }

    #[Route('/new', name: 'app_alumno_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $alumno = new Alumno();
        $form = $this->createForm(AlumnoType::class, $alumno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($alumno);
            $entityManager->flush();

            return $this->redirectToRoute('app_alumno_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alumno/new.html.twig', [
            'alumno' => $alumno,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_alumno_show', methods: ['GET'])]
    public function show(Alumno $alumno): Response
    {
        return $this->render('alumno/show.html.twig', [
            'alumno' => $alumno,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_alumno_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Alumno $alumno, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AlumnoType::class, $alumno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_alumno_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alumno/edit.html.twig', [
            'alumno' => $alumno,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_alumno_delete', methods: ['POST'])]
    public function delete(Request $request, Alumno $alumno, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$alumno->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($alumno);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_alumno_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/notas', name: 'app_alumno_notas', methods: ['GET', 'POST'])]
    public function registrarNota(Alumno $alumno, Request $request, EntityManagerInterface $entityManager): Response
    {
        $inscripcionRepository = $entityManager->getRepository(Inscripcion::class);
        $notaRepository = $entityManager->getRepository(Nota::class);

        $nota = new Nota();
        $inscripciones = $inscripcionRepository->findBy(["alumno" => $alumno]);
        $notas = [];

        $form = $this->createForm(RegNotaAlumnoType::class, null, [
            "inscripciones" => $inscripciones,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!empty($data["inscripcion"]) && !empty($data["valor"])) {
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
                $notas[] = [
                    "valor" => $n->getValor(),
                    "curso" => $n->getInscripcion()->getDictado()->getCurso()->getNombre(),
                ];
            }
        }

        return $this->render('alumno/notas.html.twig', [
            'alumno' => $alumno,
            'form' => $form,
            'notas' => $notas,
        ]);
    }

    #[Route('/{id}/inscribir-curso', name: 'app_alumno_inscribir_curso', methods: ['GET', 'POST'])]
    public function inscribirCurso(Alumno $alumno, Request $request, EntityManagerInterface $entityManager): Response
    {
        $dictadoRepository = $entityManager->getRepository(Dictado::class);
        $cursoRepository = $entityManager->getRepository(Curso::class);

        $formCurso = $this->createForm(InscribirAlumnoCursoType::class, null, [
            "cursos" => $cursoRepository->findAll(),
        ]);
        $formCurso->handleRequest($request);

        if ($formCurso->isSubmitted() && $formCurso->isValid()) {
            $data = $formCurso->getData();
            if (!empty($data["curso"]) && !empty($data["dictado"])) {
                $insc = new Inscripcion();
                $insc->setAlumno($alumno);
                $insc->setDictado($data["dictado"]);

                $entityManager->persist($insc);
                $entityManager->flush();

                return $this->redirectToRoute('app_alumno_show', ["id" => $alumno->getId()], Response::HTTP_SEE_OTHER);
            }
        }


        return $this->render('alumno/inscribir.html.twig', [
            'alumno' => $alumno,
            'form_curso' => $formCurso,
        ]);
    }
}
