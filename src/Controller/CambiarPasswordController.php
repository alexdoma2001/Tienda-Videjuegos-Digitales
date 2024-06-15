<?php
// src/Controller/CambiarPasswordController.php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\CambiarPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CambiarPasswordController extends AbstractController
{
    #[Route('/cambiar_password', name: 'app_cambiar_password')]
    public function cambiarPassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Usuario) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        $form = $this->createForm(CambiarPasswordType::class);
        $form->handleRequest($request); //Investiga que se llame por la llamada a traves de un submit

        if ($form->isSubmitted() && $form->isValid()) {
            $contraseñaActual = $form->get('passwordActual')->getData();
            $nuevaContraseña = $form->get('nuevaPassword')->get('first')->getData();
            $confirmarContraseña = $form->get('nuevaPassword')->get('second')->getData();

            //Condicionales donde hacemos todas las pruebas para que la contraseña se pueda cambiar. Si se cumplen todas, se cambia la contraseña
            if (!$passwordHasher->isPasswordValid($user, $contraseñaActual)) {
                $form->get('passwordActual')->addError(new FormError('La contraseña actual no es correcta.'));
            } elseif ($contraseñaActual === $nuevaContraseña) {
                $form->get('nuevaPassword')->get('first');
            } elseif ($nuevaContraseña !== $confirmarContraseña) {
                $form->get('nuevaPassword')->get('first');
                $form->get('nuevaPassword')->get('second');
            } else {
                //El hash es para codificar la contraseña
                $hashedPassword = $passwordHasher->hashPassword($user, $nuevaContraseña);
                $user->setPassword($hashedPassword);
                $entityManager->flush(); //Pasa la nueva contraseña a la bdd

                return $this->redirectToRoute('perfil_usuario');
            }
        }

        return $this->render('cambiar_password/index.html.twig', [
            'cambiarPasswordForm' => $form,
        ]);
    }
}
