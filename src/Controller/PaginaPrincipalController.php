<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Videojuego;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PaginaPrincipalController extends AbstractController
{
    /* Funcion para mostrar los datos*/
    #[Route('/', name: 'app_pagina_principal')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Filtrar categorias y videojuegos

        $categoriaId = $request->query->get('categoria'); // Se obtiene el id de la categoria que se selecciona en el desplegable

        /*Se seleccionan los videojuegos con el ID de la categoria*/
        if ($categoriaId) {
            $categoria = $entityManager->getRepository(Categoria::class)->find($categoriaId);
            $videojuegos = $categoria ? $categoria->getVideojuegos()->toArray() : [];
        } else {
            $videojuegos = $entityManager->getRepository(Videojuego::class)->findAll();
        }

        $categorias = $entityManager->getRepository(Categoria::class)->findAll();

        //Paginacion
        $pagina = $request->query->getInt('pagina', 1);
        $porPagina = 6;
        $totalPaginas = ceil(count($videojuegos) / $porPagina);
        $nJuegos = ($pagina - 1) * $porPagina;
        $videojuegosPaginados = array_slice($videojuegos, $nJuegos, $porPagina); //Para que nos coja solamente un numero de datos de videojuegos

        //Obtener claves disponibles de cada videojuego
        $videojuegosConClavesDisponibles = [];
        foreach ($videojuegosPaginados as $videojuego) {
            //Decimos que almacene en un array las claves donde pedido sea nulo
            $clavesDisponibles = array_filter($videojuego->getClaves()->toArray(), function ($clave) {
                return $clave->getPedido() === null;
            });
            $videojuegosConClavesDisponibles[] = [
                'videojuego' => $videojuego,
                'claves_disponibles' => $clavesDisponibles,
            ];
        }


        return $this->render('pagina_principal/index.html.twig', [
            'categorias' => $categorias,
            'videojuegosConClavesDisponibles' => $videojuegosConClavesDisponibles,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
        ]);
    }

    //Aqui lo que hago meter en el carrito el id del juego
    #[Route('/actualizar_carrito', name: 'actualizar_carrito', methods: ['POST'])]
    public function actualizarCarrito(Request $request, SessionInterface $session)
    {   
        //Esta es la unica parte implementada fuera del plazo.
        if (!$this->getUser()) {
            $this->addFlash('error', 'Debe iniciar sesión para añadir videojuegos al carrito <a href="' . $this->generateUrl('ruta_login') . '" class="btn btn-primary">Iniciar sesión</a>');
            return $this->redirectToRoute('pagina_principal_index'); 
        }
        
        //Aqui meto datos al carrito
        $session = $request->getSession();
        $id = $request->request->get('id');
        if ($id) {
            $carrito = $session->get('carrito', []);
            array_push($carrito, $id); //Metemos el id al carrito
            $session->set('carrito', $carrito); //Lo actualizamos, y metemos el carrito dentro de la sesion
        } else{
        $this->addFlash('error', 'No se ha podido meter el juego al carrito');
    }
        return $this->redirectToRoute('app_pagina_principal');
    }
}
