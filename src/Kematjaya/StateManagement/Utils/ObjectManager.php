<?php

/**
 * Description of ObjectManager
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ObjectManager {
    
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function getConfigParameter()
    {
        return $this->container->getParameter('kematjaya.state');
    }
    
    public function getModel($name)
    {
        $params = $this->getConfigParameter();
        return (isset($params["model"][$name])) ? new $params["model"][$name] : null;
    }
    
    public function getModelClass($name)
    {
        $params = $this->getConfigParameter();
        return (isset($params["model"][$name])) ? $params["model"][$name] : null;
    }
}
