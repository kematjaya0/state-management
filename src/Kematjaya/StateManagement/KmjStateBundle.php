<?php

/**
 * Description of KmjStateBundle
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KmjStateBundle extends Bundle {
    
    public function build(ContainerBuilder $container)
    {
        //$container->addCompilerPass(new SerializerConfigurationPass());
    }
}
