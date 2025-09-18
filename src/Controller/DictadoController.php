<?php

namespace App\Controller;

use App\Entity\Dictado;
use App\Entity\Inscripcion;
use App\Entity\Nota;
use App\Form\DictadoType;
use App\Form\RegNotaCursoType;
use App\Repository\DictadoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dictado')]
final class DictadoController extends AbstractController
{

    #[Route('/', name: 'app_dictado_index', methods: ['GET'])]
    public function index(DictadoRepository $dictadoRepository): Response
    {
        return $this->render('dictado/index.html.twig', [
            'dictados' => $dictadoRepository->findAll(),
        ]);
    }
    
    #[Route('/{id}', name: 'app_dictado_show', methods: ['GET', 'POST'])]
    public function show(Dictado $dictado, Request $request, EntityManagerInterface $entityManager): Response
    {
        $inscripcionRepository = $entityManager->getRepository(Inscripcion::class);
        $notaRepository = $entityManager->getRepository(Nota::class);

        $inscripciones = $inscripcionRepository->findBy(["dictado" => $dictado]);
        $inscripciones_ser = [];

        foreach ($inscripciones as $i){
            $nota = $notaRepository->findOneBy(["inscripcion" => $i]);
            $inscripciones_ser[] = [
                "id" => $i->getId(),
                "nombre" => $i->getAlumno()->getNombre(),
                "apellido" => $i->getAlumno()->getApellido(),
                "dni" => $i->getAlumno()->getDni(),
                "nota" => $nota ? $nota->getValor() : null,
            ];
        }

        $notaForm = $this->createForm(RegNotaCursoType::class, null, [
            "inscripciones" => $inscripciones,
        ]);

        $notaForm->handleRequest($request);
        if ($notaForm->isSubmitted() && $notaForm->isValid()) {
            $data = $notaForm->getData();
            if (!empty($data["alumno"]) && !empty($data["valor"]) && !empty($data["dictado"])) {
                $nota = new Nota();
                $insc = $inscripcionRepository->find($data["inscripcion"]);
                $nota->setInscripcion($insc);
                $nota->setValor($data["valor"]);

                $entityManager->persist($nota);
                $entityManager->flush();
            }
        }
        return $this->render('dictado/show.html.twig', [
            'dictado' => $dictado,
            'inscripciones' => $inscripciones_ser,
            'nota_form' => $notaForm,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dictado_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dictado $dictado, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DictadoType::class, $dictado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dictado_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dictado/edit.html.twig', [
            'dictado' => $dictado,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dictado_delete', methods: ['POST'])]
    public function delete(Request $request, Dictado $dictado, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dictado->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dictado);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dictado_index', [], Response::HTTP_SEE_OTHER);
    }
}
