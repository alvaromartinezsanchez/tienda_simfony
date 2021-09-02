<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\RequestStack;

use App\Entity\Usuario;
use App\Entity\Pedido;
use App\Entity\Producto;

class CarritoController extends AbstractController
{
    
    private $requestStack;

    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
    }

    public function mostrarCarrito(){
        $requestStack = $this->requestStack->getSession();
        $productos = array();
        $total =0;
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                array_push($productos,$pro);
                $total += $pro['precio'] * $pro['unidades'];
            }
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $productos,
            'total' => $total
        ]);
    }

    public function addProducto(Producto $producto): Response
    {
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Si numero no existe es el primer articulo del carrito
        if($requestStack->get('numero') == null){
            //Inicializamos numero a valor 1
            $requestStack->set('numero', 1);
            $numero = $requestStack->get('numero');

            //Añadimos el producto con el indice 1
            $requestStack->set($numero, array(
                'id' => $producto->getId(),
                'precio' => $producto->getPrecio(),
                'unidades' => 1,
                'producto' => $producto
            
                ));
        }else{//Si ya hay articulos en el carrito
            //obtenemos el numero de articulos existente
            $numero = $requestStack->get('numero');
            $existe = false;
            //Recorre objetos del carrito
            foreach ($requestStack as $pro) {
                if(is_array($pro)){
                    $cont=1;
                    //Si el articulo ya esta en el carrito aumenta sus unidades
                    if($pro['id'] == $producto->getId()){
                        $pro['unidades'] ++;
                        $requestStack->set($cont, $pro);
                        $existe = true;
                    }
                    $cont++;
                }
                
            }//Si el producto no existe en el carrito
            if(!$existe){
                //Añadimos el producto con el indice correspondiente
                $requestStack->set($numero, array(
                    'id' => $producto->getId(),
                    'precio' => $producto->getPrecio(),
                    'unidades' => 1,
                    'producto' => $producto
                ));
            }
        }
        //Aumenta valor de numero(indice)
        $numero = $requestStack->get('numero') + 1;
        $requestStack->set('numero', $numero);
        //Obtenemos los productos del carrito
        $productos = array();
        $total =0;
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                array_push($productos,$pro);
                $total += $pro['precio'] * $pro['unidades'];
            }
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $productos,
            'total' => $total
        ]);
    }

    public function restarProducto(Producto $producto){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Recorre objetos del carrito
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                $cont=1;
                //Si el articulo ya esta en el carrito aumenta sus unidades
                if($pro['id'] == $producto->getId()){
                    $pro['unidades'] --;
                    $requestStack->set($cont, $pro);
                }
                $cont++;
            }
        }
        //Obtenemos los productos del carrito
        $productos = array();
        $total =0;
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                array_push($productos,$pro);
                $total += $pro['precio'] * $pro['unidades'];
            }
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $productos,
            'total' => $total
        ]);
    }

    public function sumarProducto(Producto $producto){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Recorre objetos del carrito
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                $cont=1;
                //Si el articulo ya esta en el carrito aumenta sus unidades
                if($pro['id'] == $producto->getId()){
                    if ($producto->getStock() -$pro['unidades'] >= 0){
                        $pro['unidades'] ++;
                        $requestStack->set($cont, $pro); 
                    }
                }
                $cont++;
            }
        }
        //Obtenemos los productos del carrito
        $productos = array();
        $total =0;
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                array_push($productos,$pro);
                $total += $pro['precio'] * $pro['unidades'];
            }
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $productos,
            'total' => $total
        ]);
    }

    public function deleteProducto(Producto $producto){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Recorre objetos del carrito
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                $cont=1;
                //Si el articulo ya esta en el carrito aumenta sus unidades
                if($pro['id'] == $producto->getId()){
                    $requestStack->remove($cont); 
                }
                $cont++;
            }
        }
        //Obtenemos los productos del carrito
        $productos = array();
        $total =0;
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                array_push($productos,$pro);
                $total += $pro['precio'] * $pro['unidades'];
            }
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $productos,
            'total' => $total
        ]);
    }

    public function deleteAll(Producto $producto){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Recorre objetos del carrito
        foreach ($requestStack as $pro) {
            if(is_array($pro)){
                $cont=1;
                $requestStack->remove($cont); 
                $cont++;
            }
        }
        

        return $this->render('carrito/index.html.twig', [
            
        ]);
    }
}
