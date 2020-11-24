<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
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
    /**
    * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="city")
    */
   private $user;

   public function __construct()
   {
       $this->user = new ArrayCollection();
   }

   /**
    * @return Collection|User[]
    */
   public function getUser(): Collection
   {
       return $this->user;
   }

   public function addUser(User $user): self
   {
       if (!$this->user->contains($user)) {
           $this->user[] = $user;
           $user->setCity($this);
       }

       return $this;
   }

   public function removeUser(User $user): self
   {
       if ($this->user->contains($user)) {
           $this->user->removeElement($user);
           // set the owning side to null (unless already changed)
           if ($user->getCity() === $this) {
               $user->setCity(null);
           }
       }

       return $this;
   }
   public function __toString()
{
return $this->name;
}
}
