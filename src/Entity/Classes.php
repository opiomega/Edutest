<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClassesRepository")
 */
class Classes
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
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="classes")
     */
    private $students;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="classes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Test", mappedBy="classe")
     */
    private $tests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="classe")
     */
    private $modules;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EducationGroup", mappedBy="classe")
     */
    private $educationGroups;

    

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Weeks", inversedBy="classe")
     */
    private $semaine;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->tests = new ArrayCollection();
        $this->modules = new ArrayCollection();
        $this->educationGroups = new ArrayCollection();
        
        $this->semaine = new ArrayCollection();
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
            $student->setClasses($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getClasses() === $this) {
                $student->setClasses(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return $this->name;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
            $test->setClasse($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getClasse() === $this) {
                $test->setClasse(null);
            }
        }

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
            $module->setClasse($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getClasse() === $this) {
                $module->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EducationGroup[]
     */
    public function getEducationGroups(): Collection
    {
        return $this->educationGroups;
    }

    public function addEducationGroup(EducationGroup $educationGroup): self
    {
        if (!$this->educationGroups->contains($educationGroup)) {
            $this->educationGroups[] = $educationGroup;
            $educationGroup->setClasse($this);
        }

        return $this;
    }

    public function removeEducationGroup(EducationGroup $educationGroup): self
    {
        if ($this->educationGroups->contains($educationGroup)) {
            $this->educationGroups->removeElement($educationGroup);
            // set the owning side to null (unless already changed)
            if ($educationGroup->getClasse() === $this) {
                $educationGroup->setClasse(null);
            }
        }

        return $this;
    }

    

    /**
     * @return Collection|Weeks[]
     */
    public function getSemaine(): Collection
    {
        return $this->semaine;
    }

    public function addSemaine(Weeks $semaine): self
    {
        if (!$this->semaine->contains($semaine)) {
            $this->semaine[] = $semaine;
        }

        return $this;
    }

    public function removeSemaine(Weeks $semaine): self
    {
        if ($this->semaine->contains($semaine)) {
            $this->semaine->removeElement($semaine);
        }

        return $this;
    }
}
