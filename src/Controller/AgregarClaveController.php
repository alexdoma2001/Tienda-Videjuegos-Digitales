<?php

namespace App\Controller;

use App\Entity\Clave;
use App\Entity\Usuario;
use App\Entity\Videojuego;
use App\Form\AgregarClaveType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgregarClaveController extends AbstractController
{
    #[Route('/agregar_clave/{id}', name: 'app_agregar_clave')]
    public function AgregarClave(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        //Para que nos redirija si no nos hemos logeado
        $usuario = $this->getUser();
        if (!$usuario instanceof Usuario || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('pagina_principal_index');
        }
        //Recogemos el id del videojuego
        $videojuego = $entityManager->getRepository(Videojuego::class)->find($id);

        if (!$videojuego) {
            throw $this->createNotFoundException('No se ha encontrado el videojuego con ID ' . $id);
        }
        //indicamos de que videojuego va a ser la clave y creamos su form
        $clave = new Clave();
        $clave->setVideojuego($videojuego);
        $form = $this->createForm(AgregarClaveType::class, $clave);
        //Que se llega si ha sido por sun submited
        $form->handleRequest($request);
        
        //Si el form es valido y ha llegado por submit, que recoja los dos datos de los label
        if ($form->isSubmitted() && $form->isValid()) {
            $codigo = $form->get('codigo')->getData();
            $codigoConfirmado = $form->get('confirmar_codigo')->getData();
        
            //Y si los dos datos coinciden que se agrege a ese id, y si no pues no es valido y devuelve al mismo sitio
            if ($codigo !== $codigoConfirmado) {
                $form->get('codigo')->addError(new FormError('Los codigos no coinciden'));
                $this->addFlash('error', 'El formulario no es válido.');
                //Para cuando da error
                //Lo del id es para que almacene el id del videojuego en esa variable, y cuando haga refresh que pueda encontrar el id del videojuego
                return $this->redirectToRoute('app_agregar_clave', ['id' => $id]);
                
            } else { //si todo va bien lo inserta a la bdd
                $entityManager->persist($clave);
                $entityManager->flush();
                $this->addFlash('success', 'Clave añadida con éxito.');
        
                return $this->redirectToRoute('app_agregar_clave', ['id' => $id]);
            }
        } else {
            if ($form->isSubmitted()) {
                //Si no es valido el formulario por lo que sea que devuelva a la pagina principal
                $this->addFlash('error', 'El formulario no es válido.');
                return $this->redirectToRoute('pagina_principal_index');
            }
        }
        
            //Para que pueda generar el form
        return $this->render('agregar_clave/index.html.twig', [
            'form' => $form->createView(),
            'videojuego' => $videojuego,
        ]);
    }
}
