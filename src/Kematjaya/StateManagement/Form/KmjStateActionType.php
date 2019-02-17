<?php

namespace Kematjaya\StateManagement\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenEntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class KmjStateActionType extends AbstractType
{
    
    private $container;
    
    private $entityManager;
    
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager) {
        $this->container = $container;
        $this->entityManager = $entityManager;
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('state',HiddenEntityType::class, [
            'class' => $this->container->get("kematjaya.object_manager")->getModelClass("KmjState"),
        ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();
            
            $form->add('target', EntityType::class, [
                'class' => $this->container->get("kematjaya.object_manager")->getModelClass("KmjState"), 
                'query_builder' => function () use ($data) {
                    return $this->entityManager->createQueryBuilder()->select('u')->from($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"), 'u')
                        ->where('u.id != :id')
                        ->orderBy('u.sequence', 'ASC')->setParameter(':id', $data->getState()->getId());
                },
                'choice_label' => 'name', "attr" => ["class" => "form-control"]]);
            $form->add('label', TextType::class, ["attr" => ["class" => "form-control"]]);
            $form->add('description', TextareaType::class, ["attr" => ["class" => "form-control"]]);
        });
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->container->get("kematjaya.object_manager")->getModelClass("KmjStateAction"),
        ]);
    }
}
