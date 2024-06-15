<?php

// src/Controller/AgregarAdminController.php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AgregarAdminController extends AbstractController{

    #[Route('/agregar_admin', name: 'app_agregar_admin')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        //Recogemos el usuario y le asignamos el rol admin
        $usuario = $this->getUser();

        if (!$usuario instanceof Usuario || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Obtenemos la contraseña del usuario
            $primeraPassword = $form->get('plainPassword')->get('first')->getData();
            $segundaPassword = $form->get('plainPassword')->get('second')->getData();

            
            if ($primeraPassword !== $segundaPassword) {
                $form->get('plainPassword')->get('first')->addError(new FormError('Las contraseñas no coinciden.'));
            } else {
                
                $user->setPassword(
                    $userPasswordHasher->hashPassword( //Codifica y hace los inserts de la contraseña al usuario
                        $user,
                        $primeraPassword
                    )
                );
                // Establecemos el saldo saldo y roles
                $user->setSaldo(0);
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
                $entityManager->persist($user);
                $entityManager->flush();

                //redirigimos
                return $security->login($user, 'form_login', 'main');
            }
        }

        
        return $this->render('agregar_admin/index.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
