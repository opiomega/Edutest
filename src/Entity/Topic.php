<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TopicRepository")
 */
class Topic
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Weeks", inversedBy="topics")
     */
    private $week;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="Topic")
     */
    private $modules;

    

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EducationGroup", inversedBy="topics")
     */
    private $educationGroup;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Test", mappedBy="topic")
     */
    private $tests;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
       
        $this->tests = new ArrayCollection();
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
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setTopic($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getTopic() === $this) {
                $module->setTopic(null);
            }
        }

        return $this;
    }

    

    public function getEducationGroup(): ?EducationGroup
    {
        return $this->educationGroup;
    }

    public function setEducationGroup(?EducationGroup $educationGroup): self
    {
        $this->educationGroup = $educationGroup;

        return $this;
    }

    public function getWeek(): ?Weeks
    {
        return $this->week;
    }

    public function setWeek(?Weeks $week): self
    {
        $this->week = $week;

        return $this;
    }

    /**
     * @return Collection|Test[]
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->setTopic($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getTopic() === $this) {
                $test->setTopic(null);
            }
        }

        return $this;
    }
}
