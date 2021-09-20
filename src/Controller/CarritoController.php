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
        $carrito = $requestStack->get('Carrito');
        if($carrito == null){
            return $this->render('carrito/index.html.twig');
        }
        $total =0;
        foreach ($carrito as $producto_carrito) {
            $total += $producto_carrito['precio'] * $producto_carrito['unidades'];
            
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $carrito,
            'total' => $total
        ]);
    }

    public function addProducto(Producto $producto)
    {
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        if($requestStack->get('Carrito') == null){
            //Crea variable sesion carrito
            $requestStack->set('Carrito', array());
            //Almacena datos(lineas/productos) del carrito
            $carrito = $requestStack->get('Carrito');
            //Almacena cada linea/producto en el carrito
            $producto_carrito = array(
                'id' => $producto->getId(),
                'precio' => $producto->getPrecio(),
                'unidades' => 1,
                'producto' => $producto
            );
            //Añade al array carrito la linea del nuevo producto
            array_push($carrito, $producto_carrito);
            //Actualiza valor de variable de session carrito
            $requestStack->set('Carrito', $carrito);
        }else{
            //Obtiene datos(lineas/productos) del carrito
            $carrito = $requestStack->get('Carrito');
            //Existe el producto en el carrito
            $existe = false;
            foreach ($carrito as $key => $producto_carrito) {
                if($producto_carrito['id'] == $producto->getId()){
                    $existe = true;
                    //Aumenta las unidades del articulo
                    $carrito[$key]['unidades'] ++;
                }
            }
            //Actualiza valor de variable de session carrito
            $requestStack->set('Carrito', $carrito);
            //Si el producto no existe
            if(!$existe){
                //Almacena cada linea/producto en el carrito
                $producto_carrito = array(
                    'id' => $producto->getId(),
                    'precio' => $producto->getPrecio(),
                    'unidades' => 1,
                    'producto' => $producto
                );
                //Añade al array carrito la linea del nuevo producto
                array_push($carrito, $producto_carrito);
                //Actualiza valor de variable de session carrito
                $requestStack->set('Carrito', $carrito);
            }
        }
        //Obtiene precio total del carrito
        $total =0;
        foreach ($carrito as $producto_carrito) {
            $total += $producto_carrito['precio'] * $producto_carrito['unidades'];
            
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $carrito,
            'total' => $total
        ]);
    }

    public function restarProducto(Producto $producto){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Obtiene datos del carrito
        $carrito = $requestStack->get('Carrito');
        //Recorre los articulos del carrito
        foreach ($carrito as $key => $producto_carrito) {
            if($producto_carrito['id'] == $producto->getId()){
                //Resta unidad del articulo
                $carrito[$key]['unidades'] --;
                //Si las unidades se quedan en 0 elimina el registro del articulo
                if($carrito[$key]['unidades'] == 0){
                    unset($carrito[$key]);
                }
            }
        }
        //Actualiza valor de variable de session carrito
        $requestStack->set('Carrito', $carrito);

        //Obtiene precio total del carrito
        $total =0;
        foreach ($carrito as $producto_carrito) {
            $total += $producto_carrito['precio'] * $producto_carrito['unidades'];
            
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $carrito,
            'total' => $total
        ]);
    }

    public function sumarProducto(Producto $producto){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Obtiene datos del carrito
        $carrito = $requestStack->get('Carrito');
        //Recorre los articulos del carrito
        foreach ($carrito as $key => $producto_carrito) {
            if($producto_carrito['id'] == $producto->getId()){
                //Suma unidad del articulo
                $carrito[$key]['unidades'] ++;
            }
        }
        //Actualiza valor de variable de session carrito
        $requestStack->set('Carrito', $carrito);

        //Obtiene precio total del carrito
        $total =0;
        foreach ($carrito as $producto_carrito) {
            $total += $producto_carrito['precio'] * $producto_carrito['unidades'];
            
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $carrito,
            'total' => $total
        ]);
    }

    public function deleteProducto(Producto $producto){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();

        //Obtiene datos del carrito
        $carrito = $requestStack->get('Carrito');
        //Recorre los articulos del carrito
        foreach ($carrito as $key => $producto_carrito) {
            if($producto_carrito['id'] == $producto->getId()){
                //Elimina articulo del carrito
                unset($carrito[$key]);
            }
        }
        //Actualiza valor de variable de session carrito
        $requestStack->set('Carrito', $carrito);

        //Obtiene precio total del carrito
        $total =0;
        foreach ($carrito as $producto_carrito) {
            $total += $producto_carrito['precio'] * $producto_carrito['unidades'];
            
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $carrito,
            'total' => $total
        ]);
    }

    public function deleteAll(){
        //Obtiene session
        $requestStack = $this->requestStack->getSession();
        //Elimina carrito
        $requestStack->remove('Carrito');

        return $this->render('carrito/index.html.twig', [
            
        ]);
    }
}
