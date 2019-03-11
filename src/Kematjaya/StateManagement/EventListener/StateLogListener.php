<?php

/**
 * Description of StateLogListener
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kematjaya\StateManagement\Utils\EntityStateInterface;

class StateLogListener {
    
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        foreach($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof EntityStateInterface) {
                
                $logProvider = $this->container->get('kematjaya.state.state_log_provider');
                $stateLog = $logProvider->saveLog($entity);
                if($stateLog) {
                    $entityManager->persist($stateLog);
                    $classMetadata = $entityManager->getClassMetadata(get_class($stateLog));
                    $uow->computeChangeSet($classMetadata, $stateLog);
                }
                    
            }
        }
    }
}
