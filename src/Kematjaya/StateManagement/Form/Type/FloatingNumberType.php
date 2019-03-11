<?php

/**
 * Description of FloatingNumberType
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FloatingNumberType extends AbstractType {
    
    public function getParent()
    {
        return TextType::class;
    }
}
