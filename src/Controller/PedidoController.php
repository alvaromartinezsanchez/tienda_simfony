<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Pedido;
use App\Entity\Usuario;
use App\Entity\LineasPedido;
use App\Entity\Producto;
use App\Entity\Categoria;
use App\Form\PedidoRegisterType;

class PedidoController extends AbstractController
{
    
    private $requestStack;

    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
    }

    public function realizarPedido(Request $request)
    {

        $requestStack = $this->requestStack->getSession();

        //-- Pedido --
        $pedido = new Pedido();

        $form = $this->createForm(PedidoRegisterType::class, $pedido);

        //Asigna valores recibidos del del formulario al objeto relacionado
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            $pedido->setUsuario($this->getUser());
            $pedido->setProvincia($form->get('provincia')->getData());
            $pedido->setLocalidad($form->get('localidad')->getData());
            $pedido->setDireccion($form->get('direccion')->getData());
            $pedido->setEstado("Pagado");
            $pedido->setFecha(new \DateTime('now'));
            $pedido->setHora(new \DateTime('now'));

            $productos = $requestStack->get('Carrito');
            $total =0;

            foreach ($productos as $pro) {
                $total += $pro['precio'] * $pro['unidades'];
            }

            $pedido->setCoste($total);

            //Guardar Pedido
            $em = $this->getDoctrine()->getManager();
            $em->persist($pedido);
            $em->flush();

            //-- Lineas Pedido
            foreach ($productos as $linea_producto) {
                $linea_pedido = new LineasPedido();
                $linea_pedido->setPedido($pedido);

                $categoria = $this->getDoctrine()->getRepository(Categoria::class)->findBy(['id' => $linea_producto['producto']->getCategoria()]);
                $linea_producto['producto']->setCategoria($categoria[0]);

                $linea_pedido->setProducto($linea_producto['producto']);
                $linea_pedido->setUnidades($linea_producto['unidades']);
                
                $em->merge($linea_pedido);
                $em->flush();

                $pedido->addLineasPedido($linea_pedido);
                $em->refresh($pedido);
                
            }

            //Elimina productos del carrito
            $requestStack->remove('Carrito');

            return $this->render('pedido/index.html.twig', [
                'pedido' => $pedido
            ]);
        }

        
        return $this->render('pedido/direccion.html.twig', [
            'form' => $form->createView()
        ]);
        
    }
}
