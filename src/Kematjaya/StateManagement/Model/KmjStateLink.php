<?php

namespace Kematjaya\StateManagement\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class KmjStateLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Kematjaya\StateManagement\Model\KmjState", inversedBy="kmjStateLinks")
     */
    protected $state;

    /**
     * @ORM\ManyToOne(targetEntity="Kematjaya\StateManagement\Model\KmjLink", inversedBy="kmjStateLinks")
     */
    protected $link;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_show;

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

    public function getLink(): ?KmjLink
    {
        return $this->link;
    }

    public function setLink(?KmjLink $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getIsShow(): ?bool
    {
        return $this->is_show;
    }

    public function setIsShow(bool $is_show): self
    {
        $this->is_show = $is_show;

        return $this;
    }
}
