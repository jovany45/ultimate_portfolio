<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'app_projects')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findAll();
        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/{id}', name: 'app_project_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Le projet n\'existe pas.');
        }

        return $this->render('project/detail.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/new', name: 'app_project_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $project->setPhoto($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement du fichier
                }
            }

            $pdfFile = $form->get('pdf')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdfs_directory'),
                        $newFilename
                    );
                    $project->setPdf($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement du fichier
                }
            }

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_detail', ['id' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}/edit', name: 'app_project_edit')]
    public function edit(Project $project, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $project->setPhoto($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement du fichier
                }
            }

            $pdfFile = $form->get('pdf')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdfs_directory'),
                        $newFilename
                    );
                    $project->setPdf($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant le téléchargement du fichier
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_project_detail', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}