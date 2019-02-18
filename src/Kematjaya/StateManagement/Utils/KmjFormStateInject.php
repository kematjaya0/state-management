<?php

/**
 * Description of KmjFormStateInject
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenEntityType;

class KmjFormStateInject {
    
    private $entityManager;
    
    private $container;
    
    function __construct(EntityManagerInterface $entityManager, ContainerInterface $container) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    public function addFormField(FormBuilderInterface $builder, $class)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($class) {
            $data = $event->getData();
            $form = $event->getForm();
            
            foreach($this->entityManager->getClassMetadata($class)->getColumnNames() as $column) {
                if($column == 'id') {
                    continue;
                }
                
                if(!$form->has($column)) {
                    $fieldMapping = $this->entityManager->getClassMetadata($class)->getFieldMapping($column);
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
            
            $assosiationFIelds = $this->entityManager->getClassMetadata($class)->getAssociationMappings();
            foreach($assosiationFIelds as $column => $value) {
                if(isset($value["sourceToTargetKeyColumns"])) {
                    $form->add($column, EntityType::class, ['label' => $column, 'class' => (string) $value["targetEntity"],  'attr' => ['class' => 'form-control']]);
                }
            }
            
        });
    }
    
    public function injectStateForm(FormBuilderInterface $builder, $class)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($class) {
            $data = $event->getData();
            $form = $event->getForm();
            if($data->getState()){
                $myState = $this->entityManager->createQueryBuilder()->select('state')->from($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"), 'state')
                            ->where("state.id = :id")->setParameter("id", $data->getState()->getId())
                            ->getQuery()->useQueryCache(true)->getOneOrNullResult();
                
                if(count($myState->getKmjStateActions())>1) {
                    
                    $choices = array();
                    foreach($myState->getKmjStateActions() as $action) {
                        $choices[$action->getTargetObj()->getName()] = $action->getTargetObj()->getId();
                    }
                
                    $form->add('approval', ChoiceType::class, ['choices' => $choices, "attr" => ["class" => "form-control"]]);
                    $form->add('approval_description', TextareaType::class, ['required' => false, "attr" => ["class" => "form-control"]]);
                } elseif(count($myState->getKmjStateActions()) == 1) {
                   
                    $choices = array();
                    foreach($myState->getKmjStateActions() as $action) {
                        $choices[$action->getDescription()] = $action->getTargetObj()->getId();
                    }
                    
                    $form->add('approval', ChoiceType::class, ['choices' => $choices, 'expanded' => true, 'multiple' => true]);
                    $form->add('approval_description', TextareaType::class, ['required' => false, "attr" => ["class" => "form-control"]]);
                }
            }
        });
        
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($class) {
            $data = $event->getData();
            $form = $event->getForm();
            
            switch(get_class($form->get("state")->getConfig()->getType()->getInnerType())) {
                case HiddenEntityType::class:
                    $trUsulan = $form->getData();
                    if(!$trUsulan->getState()) {
                        $code = "draft";
                        $firstSTate = $this->entityManager->createQueryBuilder()
                                ->select('state')
                                ->from($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"), 'state')
                                ->where("state.obj_class = :obj_class and state.code=:code")
                                ->setParameters(new ArrayCollection(array(new Parameter("obj_class", $class), new Parameter("code", $code))))
                                ->getQuery()->useQueryCache(true)->getOneOrNullResult();
                    
                        if(!$firstSTate) {
                            throw new \Exception('state with code "draft" and obj_class "'.$class.'" not found.');
                        }
                        
                        $data["state"] = $firstSTate->getId();
                        $event->setData($data);
                    }
                    break;
                default:
                    break;
            }
            
        });
        
    }
}
