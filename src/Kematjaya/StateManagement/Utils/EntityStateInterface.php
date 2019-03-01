<?php

/**
 * Description of EntityStateInterface
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Utils;

/**
 *
 * @author NUR HIDAYAT
 */
interface EntityStateInterface {
    
    public function getState();
    
    public function getListActions();
    
    public function getApproval();
    
    public function setApproval($approval);
    
    public function getApprovalDescription();
    
    public function setApprovalDescription($approval_description);
    
    public function getPrevState();
    
    public function allowToEdit() :bool;
    
    public function allowToAction() :bool;
    
    public function getStartCode() :string;
    
    public function getStateColumnName() :string;
}
