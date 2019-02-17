<?php

/**
 * Description of KmjLinkFilterType
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;

class KmjLinkFilterType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', Filters\TextFilterType::class, ['condition_pattern' => FilterOperands::STRING_BOTH, 'attr' => ['class' => 'form-control']])
            ->add('name', Filters\TextFilterType::class, ['condition_pattern' => FilterOperands::STRING_BOTH, 'attr' => ['class' => 'form-control']])
            ->add('description', Filters\TextFilterType::class, ['condition_pattern' => FilterOperands::STRING_BOTH, 'attr' => ['class' => 'form-control']])
        ;
    }
    
    public function getBlockPrefix()
    {
        return 'kmj_link_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
