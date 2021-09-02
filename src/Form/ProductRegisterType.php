<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Image;

class ProductRegisterType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('categoria', IntegerType::class, array(
            'label' => 'Categoria'
        ))
        ->add('nombre', TextType::class, array(
            'label' => 'Nombre'
        ))
        ->add('descripcion', TextType::class, array(
            'label' => 'Descripcion'
        ))
        ->add('precio', MoneyType::class, array(
            'label' => 'Precio'
        ))
        ->add('stock', IntegerType::class, array(
            'label' => 'Stock'
        ))
        ->add('oferta', TextType::class, array(
            'label' => 'Oferta'
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