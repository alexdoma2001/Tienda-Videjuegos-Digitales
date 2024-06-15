<?php
// src/Controller/IngresarSaldoController.php
namespace App\Controller;

use App\Entity\Usuario;
use App\Form\IngresarSaldoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngresarSaldoController extends AbstractController
{
    #[Route('/ingresar_saldo', name: 'app_ingresar_saldo')]
    public function ingresarSaldo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Usuario) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        $form = $this->createForm(IngresarSaldoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dineroAIngresar = $data['dineroIngresar'];
            //Actualizamos el saldo en la bdd
            $nuevoSaldo = $user->getSaldo() + $dineroAIngresar;
            $user->setSaldo($nuevoSaldo);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Saldo ingresado exitosamente.');

            return $this->redirectToRoute('perfil_usuario');
        }

        return $this->render('ingresar_saldo/index.html.twig', [
            'IngresarSaldoForm' => $form,
        ]);
    }
}