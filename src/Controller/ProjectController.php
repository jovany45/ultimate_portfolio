<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'app_projects')]
    public function list(): Response
    {
        // Logique pour récupérer et afficher tous les projets
        return $this->render('project/list.html.twig', [
            'projects' => [], // Remplacez par la liste réelle des projets
        ]);
    }

    #[Route('/project/{id}', name: 'app_project_detail')]
    public function detail(int $id): Response
    {
        // Logique pour récupérer et afficher les détails d'un projet par son ID
        return $this->render('project/detail.html.twig', [
            'project' => null, // Remplacez par le projet réel
        ]);
    }
}
