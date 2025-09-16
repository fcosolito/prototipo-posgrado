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

#[Route('/')]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index', methods: ['GET', 'POST'])]
    public function show(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }
}
