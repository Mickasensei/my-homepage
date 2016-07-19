<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchOnGoogleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, array(
                'label' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'style' => 'width: 400px !important',
                    'placeholder' => 'Search on Google',
                )
            ));
    }
}