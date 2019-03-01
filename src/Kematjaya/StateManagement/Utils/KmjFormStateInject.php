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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Kematjaya\StateManagement\Form\Type\HiddenDateTimeType;
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
            if($data instanceof EntityStateInterface) {
                
                if(!$data->allowToEdit()) {
                    foreach($form as $k => $value) {
                        switch(get_class($form->get($k)->getConfig()->getType()->getInnerType()))  {
                            case TextType::class:
                            case NumberType::class:
                            case IntegerType::class:
                            case TextareaType::class:
                            case ChoiceType::class:
                                $form->add($k, HiddenType::class);
                                break;
                            case DateTimeType::class:
                                $form->add($k, HiddenDateTimeType::class);
                                break;
                            case EntityType::class:
                                $options = $form->get($k)->getConfig()->getOptions();
                                $form->add($k, HiddenEntityType::class, ["class" => $options["class"]]);
                                break;
                            default:
                                break;
                        }
                    }
                }
                
                if($data->allowToAction()) {
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
            }
                
        });
        
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($class) {
            $data = $event->getData();
            $form = $event->getForm();
            $obj = $form->getData();
            if(!$form->has($obj->getStateColumnName())) {
                throw new \Exception(sprintf("column '%s' not found, please create column '%s' or override the 'getStateColumnName()' methods on you entity to change column name. ", $obj->getStateColumnName(), $obj->getStateColumnName()));
            }
            
            switch(get_class($form->get($obj->getStateColumnName())->getConfig()->getType()->getInnerType())) {
                case HiddenEntityType::class:
                    if(!$obj->getState()) {
                        $firstState = $this->entityManager->createQueryBuilder()
                                ->select('state')
                                ->from($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"), 'state')
                                ->where("state.obj_class = :obj_class and state.code=:code")
                                ->setParameters(new ArrayCollection(array(new Parameter("obj_class", $class), new Parameter("code", $obj->getStartCode()))))
                                ->getQuery()->useQueryCache(true)->getOneOrNullResult();
                    
                        if(!$firstState) {
                            throw new \Exception('state with code "'.$obj->getStartCode().'" and obj_class "'.$class.'" not found.');
                        }
                        
                        $data[$obj->getStateColumnName()] = $firstState->getId();
                        $event->setData($data);
                    }
                    break;
                default:
                    break;
            }
            
        });
        
    }
}
