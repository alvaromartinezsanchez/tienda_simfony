<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Usuario;
use App\Entity\Pedido;
use App\Entity\Producto;
use App\Form\RegisterType;
use App\Form\EditType;
use App\Form\AdminRegisterType;
use App\Form\AdminEditType;

use Symfony\Component\Security\Core\User\UserInterface;

class UsuarioController extends AbstractController
{
    
    public function registro(Request $request, UserPasswordHasherInterface $encoder, SluggerInterface $slugger)
    {   
        
        $user = new Usuario();
        
        //Segun el rol del usuario que hace la llamada asinga un formulario u otro, para el admin añade el campo de rol
        if($this->getUser() && $this->getUser()->getRole() == 'ROLE_ADMIN'){
            $form = $this->createForm(AdminRegisterType::class, $user);
            $isAdmin = true;
        }else{
            //Crea Formulario de registro y lo relaciona con el objeto user
            $form = $this->createForm(RegisterType::class, $user);
            $isAdmin = false;
        }
        
        //Asigna valores recibidos del del formulario al objeto relacionado
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Modifica la propiedad rol del objeto "usuario" para guardarlo si el usuario no es administrador
            if(!$isAdmin){
                $user->setRole('ROLE_USER');
            }
            
            $user->setCreatedAt(new \DateTime('now'));
            //Cifrar Contraseña
            $encoded = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($encoded);
            
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
                $user->setImagen($newFilename);
            }


            //Guardar Usuario
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('gestion_usuarios'));
        }

        return $this->render('usuario/register.html.twig', [
            'form' => $form->createView(),
            'isAdmin' => $isAdmin
        ]);
    }

    public function login(AuthenticationUtils $authenticationUtils){
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        //var_dump($authenticationUtils);
        
        return $this->render('usuario/login.html.twig', array(
            'error' => $error,
            'last_username' => $lastUsername
        ));
    }

    public function gestionUsuarios(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request){
        
        //$query = $em->getRepository(Usuario::class)->findUsuarios();
        $dql = "SELECT u FROM App:Usuario u";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('usuario/gestionUsuarios.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function detalleUsuario(Usuario $usuario, PaginatorInterface $paginator, EntityManagerInterface $em, Request $request){

        $pedidos = count($usuario->getPedidos());
        
        if($pedidos < 1){
            return $this->render('usuario/detalleUsuario.html.twig', [
                'usuario' => $usuario
            ]);
        }else{
            $dql = "SELECT p FROM App:Pedido p WHERE p.usuario=" . $usuario->getId();
            $query = $em->createQuery($dql);

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                6 /*limit per page*/
            );

            return $this->render('usuario/detalleUsuario.html.twig', [
                'usuario' => $usuario,
                'pagination' => $pagination
            ]);
        }

        
    }

    public function editarUsuario(Request $request, Usuario $usuario, $changePassword, UserPasswordHasherInterface $encoder, SluggerInterface $slugger){
        $isAdmin = false;
        $edit = true;

        //Segun el rol del usuario que hace la llamada asinga un formulario u otro, para el admin añade el campo de rol
        if($this->getUser() && $this->getUser()->getRole() == 'ROLE_ADMIN'){
            if($changePassword){
                $form = $this->createForm(AdminRegisterType::class, $usuario);
            }else{
                $form = $this->createForm(AdminEditType::class, $usuario);
            }
            
            $isAdmin = true;
        }else{
            if($changePassword){
                $form = $this->createForm(RegisterType::class, $usuario);
            }else{
                $form = $this->createForm(EditType::class, $usuario);
            }
            
        }
        
        //Asigna valores recibidos del del formulario al objeto relacionado
        $form->handleRequest($request);
        
        //-- ENVIO DE FORMULARIO --
        if($form->isSubmitted() && $form->isValid()){
            
            //Cifrar Contraseña
            $encoded = $encoder->hashPassword($usuario, $usuario->getPassword());
            $usuario->setPassword($encoded);
            
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
                $usuario->setImagen($newFilename);
            }
            //Guardar Usuario
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return $this->redirect($this->generateUrl('detalle_usuario', ['id' => $usuario->getId()]));
        }

        return $this->render('usuario/register.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario,
            'isAdmin' => $isAdmin,
            'edit' => $edit,
            'changePassword' => $changePassword
        ]);

    }

    public function borrarUsuario(Request $request,Usuario $usuario, PaginatorInterface $paginator, EntityManagerInterface $emi){

        //Si hay Pedido del usuario cambia el valor de producto en la tabla lineas pedido a null
        if($this->getDoctrine()->getRepository(Pedido::class)->findBy(['usuario' => $usuario->getId()])){
            
            $pedidos = $this->getDoctrine()->getRepository(Pedido::class)->findBy(['usuario' => $usuario->getId()]);
            foreach ($pedidos as $pedido) {
                $usuario->removePedido($pedido);
            }
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($usuario);
        $em->flush();

        
        $dql = "SELECT u FROM App:Usuario u";
        $query = $emi->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('usuario/gestionUsuarios.html.twig', [
            'pagination' => $pagination
        ]);

    }
}
