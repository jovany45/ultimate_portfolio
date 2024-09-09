<?php
namespace App\Controller;

use App\Entity\LivreDor;
use App\Form\LivreDorEntryType;
use App\Repository\LivreDorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreDorController extends AbstractController
{
    private $livreDorRepository;
    public function __construct(LivreDorRepository $livreDorRepository)
    {
        $this->livreDorRepository = $livreDorRepository;
    }

    #[Route('/livredor', name: 'app_livredor')]
    public function list(): Response
    {
        // Logique pour récupérer et afficher les entrées du livre d'or
        $entries = $this->livreDorRepository->findAll();
        // Logique pour récupérer et afficher les entrées du livre d'or
        return $this->render('livredor/list.html.twig', [
            'entries' => $entries, // Remplacez par la liste réelle des entrées
        ]);
    }

    #[Route('/livredor/new', name: 'app_livredor_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   $entry = new LivreDor();   // Creer une nouvelle entrée
        $form = $this->createForm(LivreDorEntryType::class, $entry);

        // $form = $this->createForm(LivreDorEntryType::class);

        $form->handleRequest($request); // Traite les données soumises par l'utilisateur
        if ($form->isSubmitted() && $form->isValid()) { // Gérer le téléchargelent des fichiers
            $media = $form->get('media')->getData();
            if ($media) {
                $fileName = md5(uniqid()).'.'.$media->guessExtension();
                $media->move($this->getParameter('media_directory'), $fileName);
                $entry->setMedia($fileName);
            }
            $entityManager->persist($entry);
            $entityManager->flush();
            // Logique pour traiter le formulaire soummis
            // enregistre les donées dans la base de données
            
            return $this->redirectToRoute('app_livredor');
        }

        // Logique pour ajouter une nouvelle entrée au livre d'or
        return $this->render('livredor/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}