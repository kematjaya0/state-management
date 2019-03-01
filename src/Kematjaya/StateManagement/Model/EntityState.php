<?php

/**
 * Description of EntityState
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Model;

use Kematjaya\StateManagement\Utils\EntityStateInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Kematjaya\StateManagement\Utils\KmjStateLogProvider;

class EntityState implements EntityStateInterface {
    
    protected $kmj_state;
    
    protected $approval;
    
    protected $approval_description;
    
    protected $prev_state;
    
    protected $owner;
    
    protected $kmjStateLogProvider;
    
    public function __construct() {
        $this->kmj_state = new KmjState();
    }
    
    public function setLogProvider(KmjStateLogProvider $kmjStateLogProvider)
    {
        $this->kmjStateLogProvider = $kmjStateLogProvider;
    }
    
    public function getLogs($class = null)
    {
        if(is_null($class)) {
            $class = $this;
        }
        return $this->kmjStateLogProvider->getLogs($class);
    }
    
    public function getState() {
        return $this->kmj_state;
    }
    
    public function setOwner(UserInterface $owner)
    {
        $this->owner = $owner;
        return $this;
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function getListActions()
    {
        $kmjState = $this->getState();
        if($kmjState) {
            return $kmjState->getKmjStateActions();
        }
        
        return new ArrayCollection();
    }
    
    public function getApproval()
    {
        return $this->approval;
    }
    
    public function setApproval($approval)
    {
        if(is_array($approval)) {
            foreach($approval as $approv) {
                $this->approval = $approv;
                break;
            }
        } else {
            $this->approval = $approval;
        }
        
        
        $criteria = Criteria::create()
        ->where(Criteria::expr()->eq('target', $this->approval));
        $targetState = $this->getListActions()->matching($criteria);
        foreach($targetState as $state) {
            $this->setState($state->getTargetObj());
            break;
        }
        
        return $this;
    }
    
    public function getApprovalDescription()
    {
        return $this->approval_description;
    }
    
    public function setApprovalDescription($approval_description)
    {
        $this->approval_description = $approval_description;
        
        return $this;
    }
    
    public function getPrevState()
    {
        return $this->prev_state;
    }
    
    public function allowToEdit():bool
    {
        return true;
    }
    
    public function allowToAction():bool
    {
        return false;
    }
    
    public function getStartCode() :string
    {
        return 'draft';
    }
    
    public function getStateColumnName() :string
    {
        return 'state';
    }
    
}
