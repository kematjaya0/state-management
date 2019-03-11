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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class KmjStateLogProvider {
    
    private $requestStack;
    
    private $entityManager;
    
    private $container;
    
    private $tokenStorage;
    
    public function __construct(
        RequestStack $requestStack, 
        EntityManagerInterface $entityManager,
        ContainerInterface $container, 
        TokenStorageInterface $tokenStorage) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function saveLog(EntityStateInterface $entityState)
    {
        $kmjStateLog = $this->container->get("kematjaya.object_manager")->getModel("KmjStateLog");
        if($entityState->getPrevState() && ($entityState->getPrevState()->getId() !== $entityState->getState()->getId())) {
            $kmjStateLog->setPrevStatus($entityState->getPrevState()->getId());
            $kmjStateLog->setState($entityState->getState());
            $kmjStateLog->setCreatedAt(new \DateTime());
            $kmjStateLog->setObjClass(get_class($entityState));
            $kmjStateLog->setObjId($entityState->getId());
            if($this->tokenStorage->getToken()) {
                $user = $this->tokenStorage->getToken()->getUser();
                $kmjStateLog->setUserClass(get_class($user));
                $kmjStateLog->setUserId($user->getId());
                $kmjStateLog->setUserName($user->getNameUser());
            }
            $kmjStateLog->setDescription($entityState->getApprovalDescription());
            $kmjStateLog->setIpAddress($this->requestStack->getCurrentRequest()->getClientIp());
            
            return $kmjStateLog;
        }
        
        return null;
    }
    
    public function getLogs(EntityStateInterface $entityState)
    {
        return $this->entityManager->createQueryBuilder()->select("this")->from(KmjStateLog::class, 'this')
                ->where('this.obj_class = :obj_class AND this.obj_id = :obj_id')
                ->setParameter('obj_class', get_class($entityState))
                ->setParameter('obj_id', $entityState->getId())
                ->orderBy('this.created_at', 'DESC')->getQuery()->getResult();
    }
}
