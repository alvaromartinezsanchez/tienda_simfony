<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Image;

class EditType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('nombre', TextType::class, array(
            'label' => 'Nombre'
        ))
        ->add('apellidos', TextType::class, array(
            'label' => 'Apellido'
        ))
        ->add('email', EmailType::class, array(
            'label' => 'Email'
        ))
        ->add('foto', FileType::class, array(
            'required' => false,
            'mapped' => false,
            'constraints' => [
                new Image(['maxSize' => '1024k'])
            ]
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'Registrar'
        ));
    }

    

}