<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Entity\Usuario;
use App\Entity\Videojuego;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PHPMailer\PHPMailer\PHPMailer;


class CarritoCompraController extends AbstractController
{

    #[Route('/carrito_compra', name: 'app_carrito_compra')] //Los names es para cuando vayamos a llamar a la funcion
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Recogemos los videojuegos almacenados en la sesion del usuario
        $usuario = $this->getUser();
        $carrito = $request->getSession()->get('carrito', []);
        $carritoVacio = empty($carrito);

        if (!$usuario instanceof Usuario) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        $videojuegoRepository = $entityManager->getRepository(Videojuego::class);
        $carritoVideojuegos = [];
        $precioTotal = 0.0;

        // Creamos un array donde contamos las cantidades de cada videojuego
        foreach ($carrito as $id) {
            $videojuego = $videojuegoRepository->find($id);
            $precioTotal += $videojuego->getPrecio();
            if (isset($carritoVideojuegos[$id])) { //Si ya hay un videojuego le sumamos una cantidad
                $carritoVideojuegos[$id]['cantidad']++;
            } else {
                $carritoVideojuegos[$id] = ['cantidad' => 1, 'videojuego' => $videojuego]; //Si no hay el videojuego pues lo agrega
            }
        }

        return $this->render('carrito_compra/index.html.twig', [ //Recogemos todos los datos que va a tener el twig del carrito
            'controller_name' => 'CarritoCompraController',
            'carrito' => $carritoVideojuegos,
            'precio_total' => $precioTotal,
            'carrito_vacio' => $carritoVacio,
        ]);
    }

    #[Route('/carrito_compra/agregar_cantidad/{id}', name: 'agregar_cantidad')] //Los names es para cuando vayamos a llamar a la funcion
    public function agregarCantidad(Request $request, $id): Response
    {
        $carrito = $request->getSession()->get('carrito', []); //Recogemos el carrito del session

        // Verificamos que haya algo en el carrito
        if (!in_array($id, $carrito)) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        // Incrementar la cantidad del videojuego en el carrito
        $carrito[] = $id; // Añadir el videojuego nuevamente, simula que se ha añadido una unidad más

        // Actualizar el carrito en la sesión
        $request->getSession()->set('carrito', $carrito);

        return $this->redirectToRoute('app_carrito_compra');
    }

    #[Route('/carrito_compra/eliminar_cantidad/{id}', name: 'eliminar_cantidad')]
    public function eliminarCantidad(Request $request, $id): Response
    {
        $carrito = $request->getSession()->get('carrito', []);

        // Verificar si el videojuego ya está en el carrito
        if (!in_array($id, $carrito)) {
            // redirige
            return $this->redirectToRoute('pagina_principal_index');
        }

        // Buscar la posición del videojuego en el carrito y elimina una instancia
        $key = array_search($id, $carrito);
        unset($carrito[$key]);

       // Recargamos el carrito para que se actualice
        $request->getSession()->set('carrito', array_values($carrito)); 

        return $this->redirectToRoute('app_carrito_compra');
    }

    #[Route('/carrito_compra/eliminar_producto/{id}', name: 'eliminar_producto')]
    public function eliminarProducto(Request $request, $id): Response
    {
        $carrito = $request->getSession()->get('carrito', []);

        // Verificar si el videojuego está en el carrito
        if (!in_array($id, $carrito)) {
            return $this->redirectToRoute('pagina_principal_index');
        }

        // Busca la posición del videojuego en el carrito y lo elimina
        $carrito = array_diff($carrito, [$id]);

        // Actualiza el carrito en la sesión
        $request->getSession()->set('carrito', $carrito);

        // Redirigie de vuelta al carrito
        return $this->redirectToRoute('app_carrito_compra');
    }

    #[Route('/carrito_compra/comprar', name: 'comprar')]
    public function comprar(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Que si no tienes productos no deja comprar
        $usuario = $this->getUser();
        $carrito = $request->getSession()->get('carrito', []);
        if (!$usuario instanceof Usuario || empty($carrito)) {
            $this->addFlash('error', 'Tienes que agregar videojuegos al carrito antes de comprar');
            return $this->redirectToRoute('carrito_compra');
        }

        $videojuegoRepository = $entityManager->getRepository(Videojuego::class);
        $pedido = new Pedido();
        $pedido->setUsuario($usuario);
        $pedido->setFechaRealizado(new \DateTime());
        $precioFinal = 0.0;
        $fragmentoHTML = '';

        // Metemos los videojuegos al carrito y le sumamos la cantidad cuando detecta que el mismo id se añade desde fuera
        $videojuegosCarrito = [];
        foreach ($carrito as $id) {
            if (!isset($videojuegosCarrito[$id])) {
                $videojuegosCarrito[$id] = 1;
            } else {
                $videojuegosCarrito[$id]++;
            }
        }

        // Recorremos el array de los videojuegos que hay en carrito y lo metemos en cantidad
        foreach ($videojuegosCarrito as $id => $cantidadEnCarrito) {
            $videojuego = $videojuegoRepository->find($id);
            if (!$videojuego) {
                continue;
            }
            //calculamos el precio final de todo y si no tiene saldo, no deja relizar la compra
            $precioFinal += $videojuego->getPrecio() * $cantidadEnCarrito;
            if ($precioFinal > $usuario->getSaldo()) {
                $this->addFlash('error', 'Saldo insuficiente');
                return $this->redirectToRoute('carrito_compra');
            }

            //Accedemos a las claves de un videojuego, lo convierte a un array, y con array filter va filtrando por get pedido donde la clave esta nula
            $clavesDisponibles = count(array_filter($videojuego->getClaves()->toArray(), function ($clave) {
                return $clave->getPedido() === null;
            }));

            //Para que si no tenemos claves suficientes, nos diga que no se puede comprar
            if ($clavesDisponibles < $cantidadEnCarrito) {
                $this->addFlash('error', "Lo sentimos, no quedan suficientes claves del videojuego: {$videojuego->getNombre()} (claves disponibles: {$clavesDisponibles}, claves necesarias: {$cantidadEnCarrito}) ");
                return $this->redirectToRoute('carrito_compra');
            }

            //En esta variable vamos guardando cada videojuego que haya en el carrito y mas adelante se imprimirá en el pedido
            $fragmentoHTML .= $this->construirHtml($videojuego, $pedido, $cantidadEnCarrito);
        }

        $usuario->setSaldo($usuario->getSaldo() - $precioFinal);
        $entityManager->persist($pedido); //Actualiza las tablas
        $entityManager->persist($usuario);
        $pedido->setPrecioFinal($precioFinal);
        $entityManager->flush();

        $this->enviarCorreo($pedido, $usuario, $fragmentoHTML, $precioFinal);

        $request->getSession()->set('carrito', []);
        return $this->redirectToRoute('carrito_compra');
    }

    //Basicamente es la card de cada videojuego
    private function construirHtml(Videojuego $videojuego, Pedido $pedido, int $cantidadEnCarrito): string
    {
        $html = '';
        $clavesMetidasAlCorreo = 0;

        //Recorremos el vidoejuego y usando getClaves obtenemos los valores para el email
        foreach ($videojuego->getClaves() as $clave) {
            if ($clave->getPedido() === null && $clavesMetidasAlCorreo < $cantidadEnCarrito) { //Que coga las claves que sean nulas en pedido y que imprima una clave por cada cantidad en el carrito
                $clave->setPedido($pedido);
                $clavesMetidasAlCorreo++;

                // Construimos el html para cada juego
                $html .= '<div style="width: 18rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 0.25rem; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
                $html .= '<div style="border-bottom: 1px solid #ddd; padding: 0.5rem; text-align: center;">';
                $html .= '<img src="' . htmlspecialchars($videojuego->getUrlFoto()) . '" style="width: 100%; height: auto; border-radius: 0.25rem 0.25rem 0 0;" alt="' . htmlspecialchars($videojuego->getNombre()) . '">';
                $html .= '</div>';
                $html .= '<div style="padding: 1rem;">';
                $html .= '<h5 style="margin: 0 0 0.5rem; font-size: 1.25rem; font-weight: bold;">' . htmlspecialchars($videojuego->getNombre()) . '</h5>';
                $html .= '<p style="margin: 0 0 0.5rem; font-size: 1rem;">Precio: ' . htmlspecialchars($videojuego->getPrecio()) . '</p>';
                $html .= '<p style="margin: 0; font-size: 1rem;">Clave: ' . htmlspecialchars($clave->getCodigo()) . '</p>';
                $html .= '</div></div>';
            }
        }
        return $html;
    }
    private function enviarCorreo(Pedido $pedido, Usuario $usuario, string $htmlContent, $precioFinal)
    {
        $mail = new PHPMailer(true); 

        try {
            
            $mail->isSMTP();                                      
            $mail->Host = 'smtp.gmail.com';                       
            $mail->SMTPAuth = true;                               
            $mail->Username = 'symfonybasta@gmail.com';
            $mail->Password = 'ybcfphxhgmxqabbd';
            $mail->SMTPSecure = 'tls';                        
            $mail->Port = 587;                                    

            
            $mail->setFrom('symfonybasta@gmail.com', 'Tienda de Videojuegos');
            $mail->addAddress($usuario->getEmail());     

            
            $mail->isHTML(true);                                  
            $mail->Subject = 'Compra de videojuegos confirmada';
            //Que coja el html recogido anteriormente y le sume basicamente el precio final
            $htmlContent .= '<div style="width: 18rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 0.25rem; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
            $htmlContent .= '<p style="margin: 0; font-size: 1rem;">Precio Final: ' . htmlspecialchars($precioFinal) . '</p>';
            $htmlContent .= '</div>';
            $mail->Body    = $htmlContent; //El cuerpo del mensaje va a ser todo lo recogido anteriormente y se envia

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }
}
