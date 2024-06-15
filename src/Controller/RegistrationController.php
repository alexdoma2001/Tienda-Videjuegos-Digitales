<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Verificamos que el usuario no exista ya
            $existingUser = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('Ya existe un usuario con este correo electrónico.'));
            } else {
        
            // Obtener las contraseñas del formulario
            $primeraPassword = $form->get('plainPassword')->get('first')->getData();
            $segundaPassword = $form->get('plainPassword')->get('second')->getData();


            // Validar si las contraseñas coinciden
            if ($primeraPassword !== $segundaPassword) {
                $form->get('plainPassword')->get('first')->addError(new FormError('Las contraseñas no coinciden.'));
            } else {
                // Codificar y establecer la contraseña del usuario
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $primeraPassword
                    )
                );
                // Establecer saldo y roles
                $user->setSaldo(0);
                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
                $entityManager->flush();

                // Iniciar sesión y redirigir
                return $security->login($user, 'form_login', 'main');
                }
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
