<?php

/**
 * Description of KmjStateProvider
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kematjaya\StateManagement\Utils\EntityStateInterface;

class KmjStateProvider {
    
    private $entityManager;
    
    private $container;
    
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    public function getFirstState(EntityStateInterface $entity)
    {
       $kmjState = $this->entityManager->createQueryBuilder()
            ->select('state')
            ->from($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"), 'state')
            ->where("state.obj_class = :obj_class and state.code=:code")
            ->setParameters(new ArrayCollection(array(new Parameter("obj_class", get_class($entity)), new Parameter("code", $entity->getStartCode()))))
            ->getQuery()->useQueryCache(true)->getOneOrNullResult();
        return $kmjState;
    }
    
    public function getStateByClass($class, $codeSkip = array())
    {
        $kmjStates = $this->entityManager->createQueryBuilder()
            ->select('state')
            ->from($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"), 'state')
            ->where("state.obj_class = :obj_class");
        
        $params = array(
            new Parameter("obj_class", $class)
        );
        
        foreach($codeSkip as $k => $code) {
            $kmjStates = $kmjStates->andWhere("state.code != :code_".$k);
            $params[] = new Parameter("code_".$k, $code);
        }
        
        $kmjStates = $kmjStates->setParameters(new ArrayCollection($params))->orderBy('state.sequence', 'ASC')
            ->getQuery()->useQueryCache(true)->getResult();
        return $kmjStates;
    }
}
