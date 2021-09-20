<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PedidoRegisterType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('provincia', textType::class, array(
            'label' => 'Provincia'
        ))
        ->add('localidad', TextType::class, array(
            'label' => 'Localidad'
        ))
        ->add('direccion', TextType::class, array(
            'label' => 'Direccion'
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'Registrar'
        ));
    }

    

}