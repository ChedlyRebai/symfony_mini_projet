<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IdexController extends AbstractController
{
    //#[Route('/', name: 'homepage2')]

    public function index(): Response
    { 
        return $this->render('idex/index.html.twig', [
            'controller_name' => 'IdexController',
        ]);
    }
}
