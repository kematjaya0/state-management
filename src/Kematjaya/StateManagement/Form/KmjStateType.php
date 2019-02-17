<?php

namespace Kematjaya\StateManagement\Form;

use HaydenPierce\ClassFinder\ClassFinder;
use ReflectionClass;
use Doctrine\ORM\EntityManagerInterface;
use Kematjaya\StateManagement\Utils\EntityStateInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class KmjStateType extends AbstractType
{
    private $container;
    
    private $entityManager;
    
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    private function getChoicesClass()
    {
        $namespace = 'App\Entity';
        $classes = ClassFinder::getClassesInNamespace($namespace);
        
        $data = array(null => null);
        foreach($classes as $class) {
            $reflect = new ReflectionClass($class);
            if($reflect->implementsInterface(EntityStateInterface::class))
            {
                $data[$class] = $class;
            }
        }
        return $data;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('obj_class', ChoiceType::class, ['attr' => ['class' => 'form-control'], 'label' => 'obj_class', 'choices' => $this->getChoicesClass()]);
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            
            foreach($this->entityManager->getClassMetadata($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"))->getColumnNames() as $column) {
                if($column == 'id') {
                    continue;
                }
                
                if(!$form->has($column)) {
                    $fieldMapping = $this->entityManager->getClassMetadata($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"))->getFieldMapping($column);
                    switch($fieldMapping["type"]) {
                        case "text":
                            $form->add($column, TextareaType::class, ['label' => $column, 'attr' => ['class' => 'form-control']]);
                            break;
                        default:
                            $form->add($column, TextType::class, ['label' => $column, 'attr' => ['class' => 'form-control']]);
                            break;
                    }
                }
            }
            
            $assosiationFIelds = $this->entityManager->getClassMetadata($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"))->getAssociationMappings();
            foreach($assosiationFIelds as $column => $value) {
                if(isset($value["sourceToTargetKeyColumns"])) {
                    $form->add($column, EntityType::class, ['label' => $column, 'class' => (string) $value["targetEntity"],  'attr' => ['class' => 'form-control']]);
                }
            }
            
        });
        
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->container->get("kematjaya.object_manager")->getModelClass("KmjState"),
        ]);
    }
}
