<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Videojuego;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditarVideojuegoController extends AbstractController
{
    #[Route('/editar_videojuego/{id}', name: 'editar_videojuego', methods: ['POST'])]
    public function editarVideojuego(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        //Para que nos redirija si no nos hemos logeado
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        $videojuego = $entityManager->getRepository(Videojuego::class)->find($id);

        if (!$videojuego) {
            throw $this->createNotFoundException('No se ha encontrado el videojuego con ID ' . $id);
        }
        //Recogemos los 3 datos que queremos que se editen y se actualizan
        $videojuego->setNombre($request->request->get('nombre'));
        $videojuego->setDescripcion($request->request->get('descripcion'));
        $videojuego->setPrecio($request->request->get('precio'));

        $entityManager->flush();

        $this->addFlash('success', 'Videojuego actualizado con éxito.');

        return $this->redirectToRoute('pagina_principal_index');
    }
}
