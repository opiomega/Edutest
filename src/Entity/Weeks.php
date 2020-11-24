<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WeeksRepository")
 */
class Weeks
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
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Topic", mappedBy="week")
     */
    private $topics;

    

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Classes", mappedBy="semaine")
     */
    private $classe;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        
        $this->classe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Topic[]
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setWeek($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->contains($topic)) {
            $this->topics->removeElement($topic);
            // set the owning side to null (unless already changed)
            if ($topic->getWeek() === $this) {
                $topic->setWeek(null);
            }
        }

        return $this;
    }

    

    /**
     * @return Collection|Classes[]
     */
    public function getClasse(): Collection
    {
        return $this->classe;
    }

    public function addClasse(Classes $classe): self
    {
        if (!$this->classe->contains($classe)) {
            $this->classe[] = $classe;
            $classe->addSemaine($this);
        }

        return $this;
    }

    public function removeClasse(Classes $classe): self
    {
        if ($this->classe->contains($classe)) {
            $this->classe->removeElement($classe);
            $classe->removeSemaine($this);
        }

        return $this;
    }
}
