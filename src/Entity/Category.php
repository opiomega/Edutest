<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * 
 * @ORM\Entity
 * @Vich\Uploadable
 */

class Category
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="category")
     */
    private $Module;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Test", mappedBy="category")
     */
    private $tests;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Classes")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $classe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="levelTestType")
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Classes", mappedBy="category")
     */
    private $classes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Teacher", mappedBy="testsCategory")
     */
    private $teachers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Profession", mappedBy="categories")
     */
    private $professions;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     * @var string
     */
    private $photo;
    
    /**
     * @Vich\UploadableField(mapping="category_photos", fileNameProperty="photo")
     * @var File
     */
    private $photoFile;


    public function __construct()
    {
        $this->Module = new ArrayCollection();
        $this->tests = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->teachers = new ArrayCollection();
        $this->professions = new ArrayCollection();
    }

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
     * @return Collection|Module[]
     */
    public function getModule(): Collection
    {
        return $this->Module;
    }

    public function addModule(Module $module): self
    {
        if (!$this->Module->contains($module)) {
            $this->Module[] = $module;
            $module->setCategory($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->Module->contains($module)) {
            $this->Module->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getCategory() === $this) {
                $module->setCategory(null);
            }
        }

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
            $test->setCategory($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getCategory() === $this) {
                $test->setCategory(null);
            }
        }

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
     * @return mixed
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * @param mixed $classe
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;
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
            $student->setLevelTestType($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getLevelTestType() === $this) {
                $student->setLevelTestType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classes[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classes $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->setCategory($this);
        }

        return $this;
    }

    public function removeClass(Classes $class): self
    {
        if ($this->classes->contains($class)) {
            $this->classes->removeElement($class);
            // set the owning side to null (unless already changed)
            if ($class->getCategory() === $this) {
                $class->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Teacher[]
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): self
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers[] = $teacher;
            $teacher->setTestsCategory($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): self
    {
        if ($this->teachers->contains($teacher)) {
            $this->teachers->removeElement($teacher);
            // set the owning side to null (unless already changed)
            if ($teacher->getTestsCategory() === $this) {
                $teacher->setTestsCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Profession[]
     */
    public function getProfessions(): Collection
    {
        return $this->professions;
    }

    public function addProfession(Profession $profession): self
    {
        if (!$this->professions->contains($profession)) {
            $this->professions[] = $profession;
            $profession->addCategory($this);
        }

        return $this;
    }

    public function removeProfession(Profession $profession): self
    {
        if ($this->professions->contains($profession)) {
            $this->professions->removeElement($profession);
            $profession->removeCategory($this);
        }

        return $this;
    }

    public function setPhotoFile(File $photo = null)
    {
        $this->photoFile = $photo;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($photo) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->photoUpdatedAt = new \DateTime('now');
        }
    }

    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

   

    public function getPhotoUpdatedAt(): ?\DateTimeInterface
    {
        return $this->photoUpdatedAt;
    }

    public function setPhotoUpdatedAt(?\DateTimeInterface $photoUpdatedAt): self
    {
        $this->photoUpdatedAt = $photoUpdatedAt;

        return $this;
    }

   

    
    
}
