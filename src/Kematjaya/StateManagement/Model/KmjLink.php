<?php

namespace Kematjaya\StateManagement\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class KmjLink
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="Kematjaya\StateManagement\Model\KmjStateLink", mappedBy="link")
     */
    protected $kmjStateLinks;

    public function __construct()
    {
        $this->kmjStateLinks = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
            $kmjStateLink->setLink($this);
        }

        return $this;
    }

    public function removeKmjStateLink(KmjStateLink $kmjStateLink): self
    {
        if ($this->kmjStateLinks->contains($kmjStateLink)) {
            $this->kmjStateLinks->removeElement($kmjStateLink);
            // set the owning side to null (unless already changed)
            if ($kmjStateLink->getLink() === $this) {
                $kmjStateLink->setLink(null);
            }
        }

        return $this;
    }
}
