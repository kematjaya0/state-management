<?php

namespace Kematjaya\StateManagement\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class KmjStateAction implements ObjectManagerAware
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Kematjaya\StateManagement\Model\KmjState", inversedBy="kmjStateActions")
     */
    protected $state;

    /**
     * @ORM\Column(type="integer")
     */
    protected $target;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    protected $entityManager;
    
    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata)
    {
        $this->entityManager = $objectManager;
    }
    
    public function getTargetObj()
    {
        $q = $this->entityManager->createQueryBuilder()
            ->select('this')->from(KmjState::class, 'this')
            ->where('this.id = :id')
            ->setParameter('id', $this->getTarget())
            ->getQuery()->useQueryCache(true)->getOneOrNullResult();
        return $q;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getState(): ?KmjState
    {
        return $this->state;
    }

    public function setState(?KmjState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getTarget(): ?int
    {
        return $this->target;
    }

    public function setTarget(?KmjState $target): self
    {
        $this->target = $target->getId();

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
