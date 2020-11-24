<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Days
 *
 * @ORM\Table(name="days")
 * @ORM\Entity(repositoryClass="App\Repository\DaysRepository")
 */
class Days
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Counsling", mappedBy="day")
     */
    private $counslings;

    public function __construct()
    {
        $this->counslings = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Days
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Collection|Counsling[]
     */
    public function getCounslings(): Collection
    {
        return $this->counslings;
    }

    public function addCounsling(Counsling $counsling): self
    {
        if (!$this->counslings->contains($counsling)) {
            $this->counslings[] = $counsling;
            $counsling->setDay($this);
        }

        return $this;
    }

    public function removeCounsling(Counsling $counsling): self
    {
        if ($this->counslings->contains($counsling)) {
            $this->counslings->removeElement($counsling);
            // set the owning side to null (unless already changed)
            if ($counsling->getDay() === $this) {
                $counsling->setDay(null);
            }
        }

        return $this;
    }
}
