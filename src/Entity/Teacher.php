<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeacherRepository")
 */
class Teacher
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
    private $firstname;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;
    /**
     * @ORM\Column(type="string", length=255)
     */
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $university;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $score;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniversity(): ?string
    {
        return $this->university;
    }

    public function setUniversity(string $university): self
    {
        $this->university = $university;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(string $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="teacher")
     */
    private $modules;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="teacher")
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EducationGroup", mappedBy="teacher")
     */
    private $educationGroups;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="teachers")
     */
    private $testsCategory;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->educationGroups = new ArrayCollection();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString() {
        return $this->firstname;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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
            $module->setTeacher($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getTeacher() === $this) {
                $module->setTeacher(null);
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
            $student->setTeacher($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getTeacher() === $this) {
                $student->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
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
            $educationGroup->setTeacher($this);
        }

        return $this;
    }

    public function removeEducationGroup(EducationGroup $educationGroup): self
    {
        if ($this->educationGroups->contains($educationGroup)) {
            $this->educationGroups->removeElement($educationGroup);
            // set the owning side to null (unless already changed)
            if ($educationGroup->getTeacher() === $this) {
                $educationGroup->setTeacher(null);
            }
        }

        return $this;
    }

    public function getTestsCategory(): ?Category
    {
        return $this->testsCategory;
    }

    public function setTestsCategory(?Category $testsCategory): self
    {
        $this->testsCategory = $testsCategory;

        return $this;
    }
    public function getFirstAndLstname() {
        return $this->firstname.' '.$this->lastname;
    }
}
