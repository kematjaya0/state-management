<?php

namespace Kematjaya\StateManagement\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class KmjStateLog implements ObjectManagerAware
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $prev_status;

    /**
     * @ORM\ManyToOne(targetEntity="Kematjaya\StateManagement\Model\KmjState", inversedBy="kmjStateLogs")
     */
    protected $state;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $obj_class;

    /**
     * @ORM\Column(type="integer")
     */
    protected $obj_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $ip_address;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $user_class;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $user_name;
    
    private $entityManager;
    
    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata) {
        $this->entityManager = $objectManager;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPrevStatus(): ?int
    {
        return $this->prev_status;
    }

    public function getPrevState()
    {
        return $this->entityManager->createQueryBuilder()->select("this")->from(KmjState::class, 'this')
            ->where("this.id = :id")->setParameter("id", $this->getPrevStatus())->getQuery()->getOneOrNullResult();
    }
    
    public function setPrevStatus(?int $prev_status): self
    {
        $this->prev_status = $prev_status;

        return $this;
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

    public function getObjClass(): ?string
    {
        return $this->obj_class;
    }

    public function setObjClass(string $obj_class): self
    {
        $this->obj_class = $obj_class;

        return $this;
    }

    public function getObjId(): ?int
    {
        return $this->obj_id;
    }

    public function setObjId(int $obj_id): self
    {
        $this->obj_id = $obj_id;

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

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): self
    {
        $this->ip_address = $ip_address;

        return $this;
    }
    
    public function getUserClass(): ?string
    {
        return $this->user_class;
    }

    public function setUserClass(string $user_class): self
    {
        $this->user_class = $user_class;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
    
    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUsername(?string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }
    
    public function getUser()
    {
        return $this->entityManager->createQueryBuilder()->select("this")->from($this->getUserClass(), 'this')
            ->where("this.id = :id")->setParameter("id", $this->getUserId())->getQuery()->getOneOrNullResult();
    }
}
