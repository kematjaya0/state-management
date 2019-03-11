<?php

/**
 * Description of KmjStateFilterType
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use HaydenPierce\ClassFinder\ClassFinder;
use Kematjaya\StateManagement\Utils\EntityStateInterface;
use ReflectionClass;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class KmjStateFilterType extends AbstractType {
    
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
        $builder
            ->add('obj_class', ChoiceType::class, ['attr' => ['class' => 'form-control'],'required' => false, 'label' => 'obj_class', 'choices' => $this->getChoicesClass()])
            ->add('code', Filters\TextFilterType::class, ['condition_pattern' => FilterOperands::STRING_BOTH, 'attr' => ['class' => 'form-control']])
            ->add('name', Filters\TextFilterType::class, ['condition_pattern' => FilterOperands::STRING_BOTH, 'attr' => ['class' => 'form-control']])
            ->add('sequence', Filters\TextFilterType::class, ['condition_pattern' => FilterOperands::STRING_BOTH, 'attr' => ['class' => 'form-control']])
        ;
    }
    
    public function getBlockPrefix()
    {
        return 'kmj_state_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
