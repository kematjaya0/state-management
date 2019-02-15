<?php

namespace Kematjaya\StateManagement\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class KmjState
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sequence;

    /**
     * @ORM\OneToMany(targetEntity="Kematjaya\StateManagement\Model\KmjStateLink", mappedBy="state")
     */
    protected $kmjStateLinks;

    /**
     * @ORM\OneToMany(targetEntity="Kematjaya\StateManagement\Model\KmjStateAction", mappedBy="state")
     */
    protected $kmjStateActions;

    /**
     * @ORM\OneToMany(targetEntity="Kematjaya\StateManagement\Model\KmjStateLog", mappedBy="state")
     */
    protected $kmjStateLogs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $obj_class;

    public function __construct()
    {
        $this->kmjStateLinks = new ArrayCollection();
        $this->kmjStateActions = new ArrayCollection();
        $this->kmjStateLogs = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getName();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return Collection|KmjStateLink[]
     */
    public function getKmjStateLinks(): Collection
    {
        return $this->kmjStateLinks;
    }

    public function addKmjStateLink(KmjStateLink $kmjStateLink): self
    {
        if (!$this->kmjStateLinks->contains($kmjStateLink)) {
            $this->kmjStateLinks[] = $kmjStateLink;
            $kmjStateLink->setState($this);
        }

        return $this;
    }

    public function removeKmjStateLink(KmjStateLink $kmjStateLink): self
    {
        if ($this->kmjStateLinks->contains($kmjStateLink)) {
            $this->kmjStateLinks->removeElement($kmjStateLink);
            // set the owning side to null (unless already changed)
            if ($kmjStateLink->getState() === $this) {
                $kmjStateLink->setState(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|KmjStateAction[]
     */
    public function getKmjStateActions(): Collection
    {
        return $this->kmjStateActions;
    }

    public function addKmjStateAction(KmjStateAction $kmjStateAction): self
    {
        if (!$this->kmjStateActions->contains($kmjStateAction)) {
            $this->kmjStateActions[] = $kmjStateAction;
            $kmjStateAction->setState($this);
        }

        return $this;
    }

    public function removeKmjStateAction(KmjStateAction $kmjStateAction): self
    {
        if ($this->kmjStateActions->contains($kmjStateAction)) {
            $this->kmjStateActions->removeElement($kmjStateAction);
            // set the owning side to null (unless already changed)
            if ($kmjStateAction->getState() === $this) {
                $kmjStateAction->setState(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|KmjStateLog[]
     */
    public function getKmjStateLogs(): Collection
    {
        return $this->kmjStateLogs;
    }

    public function addKmjStateLog(KmjStateLog $kmjStateLog): self
    {
        if (!$this->kmjStateLogs->contains($kmjStateLog)) {
            $this->kmjStateLogs[] = $kmjStateLog;
            $kmjStateLog->setState($this);
        }

        return $this;
    }

    public function removeKmjStateLog(KmjStateLog $kmjStateLog): self
    {
        if ($this->kmjStateLogs->contains($kmjStateLog)) {
            $this->kmjStateLogs->removeElement($kmjStateLog);
            // set the owning side to null (unless already changed)
            if ($kmjStateLog->getState() === $this) {
                $kmjStateLog->setState(null);
            }
        }

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
}
