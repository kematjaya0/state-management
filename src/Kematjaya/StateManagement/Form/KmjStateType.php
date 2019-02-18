<?php

namespace Kematjaya\StateManagement\Form;

use HaydenPierce\ClassFinder\ClassFinder;
use ReflectionClass;
use Kematjaya\StateManagement\Utils\EntityStateInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class KmjStateType extends AbstractType
{
    private $container;
    
    public function __construct(ContainerInterface $container) {
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
        
        $this->container->get("kematjaya.form_state_inject")->addFormField($builder, $this->container->get("kematjaya.object_manager")->getModelClass("KmjState"));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->container->get("kematjaya.object_manager")->getModelClass("KmjState"),
        ]);
    }
}
