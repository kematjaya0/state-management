<?php

namespace Kematjaya\StateManagement\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class KmjStateLog
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
}
