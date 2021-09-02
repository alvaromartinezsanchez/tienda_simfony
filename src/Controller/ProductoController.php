<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Producto;
use App\Entity\Categoria;
use App\Entity\LineasPedido;
use App\Form\ProductRegisterType;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductoController extends AbstractController
{
    
    public function crearProducto(Request $request, SluggerInterface $slugger): Response
    {
        $producto = new Producto();

        $form = $this->createForm(ProductRegisterType::class, $producto);

        //Asigna valores recibidos del del formulario al objeto relacionado
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Fecha
            $producto->setFecha(new \DateTime('now'));
            //Categoria
            $categoria_repo = $this->getDoctrine()->getRepository(Categoria::class);
            $categoria = $categoria_repo->findOneBy(['id' => $form->get('categoria')->getData()]);
            $producto->setCategoria($categoria);
            //Imagen
            $imagen_File = $form->get('foto')->getData();
            if ($imagen_File) {
                $originalFilename = pathinfo($imagen_File->getClientOriginalName(), PATHINFO_FILENAME);
                // Modifica el nombre de la imagen y le asigna un id unico
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagen_File->guessExtension();

                // Mueve la imagen al directorio uploads/imagenes (declarado en services.yaml)
                try {
                    $imagen_File->move(
                        $this->getParameter('imagenes'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error al subir la imagen..!');
                }

                // Guarda la imagen al objeto usuario
                $producto->setImagen($newFilename);
            }
            //Guardar Producto
            $em = $this->getDoctrine()->getManager();
            $em->persist($producto);
            $em->flush();
        }

        return $this->render('producto/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function mostrarProductos(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request){
        
        $dql = "SELECT p FROM App:Producto p";
        $query = $em->getRepository(Producto::class)->findByProductos();
        

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('producto/mostrarProductos.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function gestionProductos(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request){
        
        $query = $em->getRepository(Producto::class)->findByProductos();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('producto/gestionProductos.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function detalleProducto(Producto $producto){
        return $this->render('producto/detalleProducto.html.twig', [
            'producto' => $producto
        ]);
    }

    public function editarProducto(Request $request, Producto $producto, $categoria, SluggerInterface $slugger){
        
        $producto->setCategoria($categoria);
        
        $form = $this->createForm(ProductRegisterType::class, $producto);

        //Asigna valores recibidos del del formulario al objeto relacionado
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Fecha
            $producto->setFecha(new \DateTime('now'));
            //Categoria
            $categoria_repo = $this->getDoctrine()->getRepository(Categoria::class);
            $categoria = $categoria_repo->findOneBy(['id' => $form->get('categoria')->getData()]);
            $producto->setCategoria($categoria);

            //Imagen
            $imagen_File = $form->get('foto')->getData();
            if ($imagen_File) {
                $originalFilename = pathinfo($imagen_File->getClientOriginalName(), PATHINFO_FILENAME);
                // Modifica el nombre de la imagen y le asigna un id unico
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagen_File->guessExtension();

                // Mueve la imagen al directorio uploads/imagenes (declarado en services.yaml)
                try {
                    $imagen_File->move(
                        $this->getParameter('imagenes'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error al subir la imagen..!');
                }

                // Guarda la imagen al objeto usuario
                $producto->setImagen($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($producto);
            $em->flush();

            return $this->redirect($this->generateUrl('detalle_producto', ['id' => $producto->getId()]));
        }

        return $this->render('producto/register.html.twig', [
            'edit' => true,
            'form' => $form->createView()
        ]);
    }

    public function borraProducto(Request $request,Producto $producto, PaginatorInterface $paginator){

        //Si hay lineasPedido con este producto cambia el valor de producto en la tabla lineas pedido a null
        if($this->getDoctrine()->getRepository(LineasPedido::class)->findBy(['producto' => $producto->getId()])){
            
            $lineas_pedido = $this->getDoctrine()->getRepository(LineasPedido::class)->findBy(['producto' => $producto->getId()]);
            foreach ($lineas_pedido as $linea_pedido) {
                $producto->removeLineasPedido($linea_pedido);
            }
        }
        
        
        $em = $this->getDoctrine()->getManager();

        $em->remove($producto);
        $em->flush();

        $query = $em->getRepository(Producto::class)->findByProductos();
        //$query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('producto/gestionProductos.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
