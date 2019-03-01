<?php

/**
 * Description of PostOwnerStateListener
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Kematjaya\StateManagement\Utils\EntityStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostOwnerStateListener {
    
    private $container;
    
    private $tokenStorage;
    
    public function __construct(ContainerInterface $container, TokenStorageInterface $tokenStorage)
    {
        $this->container = $container;
        
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof EntityStateInterface && !$entity->getOwner()) {
            $logProvider = $logProvider = $this->container->get('kematjaya.state.state_log_provider');
            $entity->setLogProvider($logProvider);
            $entity->setOwner($this->tokenStorage->getToken()->getUser());
        }
    }
    
}
