<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Videojuego;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class EliminarVideojuegoController extends AbstractController
{
    #[Route('/eliminar_videojuego/{id}', name: 'eliminar_videojuego', methods: ['POST'])]
    public function eliminarVideojuego(int $id, Request $request, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
                //Para que nos redirija si no nos hemos logeado
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        //Esto del token y demas es porque me salia un error de csrtToken invalid, o algo asi. Por lo que he visto es para verificar que eres un usuario logueado y tal que quiera hacer estas cosas
        $token = new CsrfToken('delete'.$id, $request->request->get('_token'));

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $videojuego = $entityManager->getRepository(Videojuego::class)->find($id);

        if (!$videojuego) {
            throw $this->createNotFoundException('No se ha encontrado el videojuego con ID ' . $id);
        }

        // Eliminamos las claves del videojuego
        foreach ($videojuego->getClaves() as $clave) {
            $entityManager->remove($clave);
        }

        // Eliminar el videojuego
        $entityManager->remove($videojuego);
        $entityManager->flush();


        return $this->redirectToRoute('pagina_principal_index');
    }
}
