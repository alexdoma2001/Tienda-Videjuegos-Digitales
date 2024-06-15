<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Videojuego;
use App\Form\AgregarVideojuegoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgregarVideojuegoController extends AbstractController
{
    #[Route('agregar_videojuego', name: 'app_videojuego_nuevo')]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {        
        //Para que nos redirija si no nos hemos logeado
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('pagina_principal_index');
        }


        $videojuego = new Videojuego();
        $form = $this->createForm(AgregarVideojuegoType::class, $videojuego);

        $form->handleRequest($request); //Indica si se ha enviado el formulario o no

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($videojuego); //Crea la instancia objeto de tipo videojuego
            $entityManager->flush();

            return $this->redirectToRoute('app_pagina_principal');
        }

        return $this->render('agregar_videojuego/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
