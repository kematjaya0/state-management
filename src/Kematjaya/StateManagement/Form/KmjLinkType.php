<?php

namespace Kematjaya\StateManagement\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class KmjLinkType extends AbstractType
{
    private $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, ['label' => 'code', 'attr' => ['class' => 'form-control']])
            ->add('name', TextType::class, ['label' => 'name', 'attr' => ['class' => 'form-control']])
            ->add('description', TextareaType::class, ['label' => 'description', 'required' => false, 'attr' => ['class' => 'form-control']])
        ;
        
        $this->container->get("kematjaya.form_state_inject")->addFormField($builder, $this->container->get("kematjaya.object_manager")->getModelClass("KmjLink"));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->container->get("kematjaya.object_manager")->getModelClass("KmjLink")
        ]);
    }
}
