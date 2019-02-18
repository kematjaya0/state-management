<?php

/**
 * Description of KmjStateLogProvider
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Utils;

use Kematjaya\StateManagement\Utils\EntityStateInterface;
use Kematjaya\StateManagement\Model\KmjStateLog;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class KmjStateLogProvider {
    
    private $requestStack;
    
    private $entityManager;
    
    private $container;
    
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager,ContainerInterface $container) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    public function saveLog(EntityStateInterface $entityState)
    {
        $kmjStateLog = $this->container->get("kematjaya.object_manager")->getModel("KmjStateLog");
        if($entityState->getPrevState()) {
            $kmjStateLog->setPrevStatus($entityState->getPrevState()->getId());
        }
        
        $kmjStateLog->setState($entityState->getState());
        $kmjStateLog->setCreatedAt(new \DateTime());
        $kmjStateLog->setObjClass(get_class($entityState));
        $kmjStateLog->setObjId($entityState->getId());
        $kmjStateLog->setDescription($entityState->getApprovalDescription());
        $kmjStateLog->setIpAddress($this->requestStack->getCurrentRequest()->getClientIp());
        
        return $kmjStateLog;
    }
    
    public function getLogs(EntityStateInterface $entityState)
    {
        return $this->entityManager->createQueryBuilder()->select("this")->from(KmjStateLog::class, 'this')
                ->where('this.obj_class = :obj_class')->setParameter('obj_class', get_class($entityState))
                ->orderBy('this.created_at', 'DESC')->getQuery()->getResult();
    }
}
