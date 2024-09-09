<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonHistoireController extends AbstractController
{
    #[Route('/mon_histoire', name: 'app_mon_histoire')]
    public function index(): Response
    {
        return $this->render('mon_histoire/mon_histoire.html.twig');
    }
}