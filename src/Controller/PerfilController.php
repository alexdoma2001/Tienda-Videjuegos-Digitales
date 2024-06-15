<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PerfilController extends AbstractController
{
    #[Route('/perfil', name: 'app_perfil')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Usuario) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        //Datos de la paginacion
        $pedidos = $user->getPedidos();
        $totalPedidos = count($pedidos);
        $limite = 5; // Número de pedidos por página
        $pagina = max(1, $request->query->getInt('pagina', 1)); // Página actual
        $nElementos = ($pagina - 1) * $limite;
        $paginacionPedidos = array_slice($pedidos->toArray(), $nElementos, $limite); //Array slice es lo que hace que los pedidos se dividan de x en x

        return $this->render('perfil/index.html.twig', [
            'controller_name' => 'PerfilController',
            'pedidos' => $paginacionPedidos,
            'paginaActual' => $pagina,
            'totalPaginas' => ceil($totalPedidos / $limite),
        ]);
        
        $pedidosConClaves = $pedidos->filter(function ($pedido) {
            return $pedido->getClaves()->count() > 0; //Que coja los pedidos donde las claves sea mayor a 0
        });
        
        return $this->render('perfil/index.html.twig', [
            'pedidos' => $pedidosConClaves,
        ]);
    }

    #[Route('/eliminar_perfil', name: 'eliminar_cuenta', methods: ['POST'])]
    public function eliminarCuenta(EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->getUser();

        if ($user instanceof Usuario) {
            $entityManager->remove($user);
            $entityManager->flush();

            // Desloguear al usuario. Esto se pone para que cuando elimine al usuario. La aplicacion desloguee al usuario y no de error al intentar buscar de nuevo el id
            $tokenStorage->setToken(null);
        }

        return $this->redirectToRoute('pagina_principal_index');
    }

}
