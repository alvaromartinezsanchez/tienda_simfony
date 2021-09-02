<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\RequestStack;

use App\Entity\Pedido;
use App\Entity\Usuario;
use App\Entity\LineasPedido;
use App\Entity\Producto;
use App\Entity\Categoria;

class PedidoController extends AbstractController
{
    
    private $requestStack;

    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
    }

    public function realizarPedido()
    {

        $requestStack = $this->requestStack->getSession();

        //-- Pedido --
        $pedido = new Pedido();
        $pedido->setUsuario($this->getUser());
        $pedido->setProvincia("Murcia");
        $pedido->setLocalidad("Alhama de murcia");
        $pedido->setDireccion("C/General Garcia Diaz");
        $pedido->setEstado("Pagado");
        $pedido->setFecha(new \DateTime('now'));
        $pedido->setHora(new \DateTime('now'));

        $productos = array();
        $total =0;
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                array_push($productos, $pro);
                $total += $pro['precio'] * $pro['unidades'];
            }
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
            $linea_pedido->setProducto($linea_producto['producto']);
            $linea_pedido->setUnidades($linea_producto['unidades']);
            $em->persist($linea_pedido);
            $em->flush();
        }



        return $this->render('pedido/index.html.twig', [
            'pedido' => $pedido
        ]);

        /*$em = $this->getDoctrine()->getManager();

        echo "-----------PEDIDOS-------<br>";

        $pedido_repo = $this->getDoctrine()->getRepository(Pedido::class);
        $pedidos = $pedido_repo->findAll();

        foreach ($pedidos as $pedido) {
            echo $pedido->getProvincia() . " : " . $pedido->getUsuario()->getNombre() . "<br>";

            foreach ($pedido->getLineasPedido() as $lineaPedido) {
                echo " - " . $lineaPedido->getPedido()->getId() . $lineaPedido->getProducto()->getId() . $lineaPedido->getUnidades() . "<br>";
            }
        }

        echo "---------USUARIOS------<br>";

        $usuario_repo = $this->getDoctrine()->getRepository(Usuario::class);
        $usuarios = $usuario_repo->findAll();

        foreach ($usuarios as $usuario) {
            echo $usuario->getNombre() ."<br>";
            
            foreach ($usuario->getPedidos() as $pedido) {
                echo $pedido->getProvincia() . "<br>";
            }
        }

        echo "---------Lineas Pedido------<br>";
        $lineaPedido_repo = $this->getDoctrine()->getRepository(LineasPedido::class);
        $lineasPedidos = $lineaPedido_repo->findAll();

        foreach ($lineasPedidos as $lineaPedido) {
            echo $lineaPedido->getPedido()->getId() . "<br>";
            echo $lineaPedido->getProducto()->getNombre() . "<br>";
        }

        echo "---------PRODUCTO------<br>";
        $producto_repo = $this->getDoctrine()->getRepository(Producto::class);
        $productos = $producto_repo->findAll();

        foreach ($productos as $producto) {
            echo $producto->getNombre() . "<br>";
            
            foreach ($producto->getLineasPedido() as $productoEnLineaPedido) {
                echo $productoEnLineaPedido->getId() . "<br>";
            }
        }

        echo "---------Categoria------<br>";
        $categoria_repo = $this->getDoctrine()->getRepository(Categoria::class);
        $categorias = $categoria_repo->findAll();

        foreach ($categorias as $categoria) {
            echo $categoria->getNombre() . "<br>";
            
            foreach ($categoria->getProductos() as $producto) {
                echo " - " . $producto->getNombre();
            }
        }


        return $this->render('pedido/index.html.twig', [
            'usuarios' => $usuarios
        ]);
        */
    }
}
