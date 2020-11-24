<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EducationGroupRepository")
 */
class EducationGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="educationGroup")
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="educationGroups")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $teacher;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Test", mappedBy="educationGroup")
     */
    private $tests;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Student", inversedBy="educationGroups")
     */
    private $students;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Seance", mappedBy="educationgroup")
     */
    private $seances;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Counsling", mappedBy="educationgroup")
     */
    private $counslings;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Classes", inversedBy="educationGroups")
     */
    private $classe;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $online;

    public function __construct()
    {
        $this->module = new ArrayCollection();
        $this->tests = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->seances = new ArrayCollection();
        $this->counslings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Module[]
     */
    public function getModule(): Collection
    {
        return $this->module;
    }

    public function addModule(Module $module): self
    {
        if (!$this->module->contains($module)) {
            $this->module[] = $module;
            $module->setEducationGroup($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->module->contains($module)) {
            $this->module->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getEducationGroup() === $this) {
                $module->setEducationGroup(null);
            }
        }

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

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
            $test->setEducationGroup($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getEducationGroup() === $this) {
                $test->setEducationGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->addEducationGroup($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            $student->removeEducationGroup($this);
        }

        return $this;
    }
    
    /**
     * @return Collection|Seance[]
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->seances->contains($seance)) {
            $this->seances[] = $seance;
            $seance->setEducationgroup($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        if ($this->seances->contains($seance)) {
            $this->seances->removeElement($seance);
            // set the owning side to null (unless already changed)
            if ($seance->getEducationgroup() === $this) {
                $seance->setEducationgroup(null);
            }
        }

        return $this;
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
            $counsling->setEducationgroup($this);
        }

        return $this;
    }

    public function removeCounsling(Counsling $counsling): self
    {
        if ($this->counslings->contains($counsling)) {
            $this->counslings->removeElement($counsling);
            // set the owning side to null (unless already changed)
            if ($counsling->getEducationgroup() === $this) {
                $counsling->setEducationgroup(null);
            }
        }

        return $this;
    }

    public function getClasse(): ?Classes
    {
        return $this->classe;
    }

    public function setClasse(?Classes $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(?bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
