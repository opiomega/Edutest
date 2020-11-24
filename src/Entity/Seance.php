<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Seance
 *
 * @ORM\Table(name="seance")
 * @ORM\Entity(repositoryClass="App\Repository\SeanceRepository")
 */
class Seance
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
     * @ORM\Column(name="beginTime", type="string")
     */
    private $beginTime;

    /**
     * @var string
     *
     * @ORM\Column(name="endTime", type="string")
     */
    private $endTime;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="Days")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $day;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="Classes")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $classe;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EducationGroup", inversedBy="seances")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $educationgroup;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentAbsent", mappedBy="seance")
     */
    private $studentAbsents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TeachersAbsent", mappedBy="seance")
     */
    private $teachersAbsents;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module", inversedBy="seances")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $Module;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateend;

    public function __construct()
    {
        $this->studentAbsents = new ArrayCollection();
        $this->teachersAbsents = new ArrayCollection();
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
     * @return Seance
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
     * Set description
     *
     * @param string $description
     * @return Seance
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getBeginTime()
    {
        return $this->beginTime;
    }

    /**
     * @param string $beginTime
     */
    public function setBeginTime($beginTime)
    {
        $this->beginTime = $beginTime;
    }

    /**
     * @return string
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param string $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @param mixed $teacher
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
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

    public function getEducationgroup(): ?EducationGroup
    {
        return $this->educationgroup;
    }

    public function setEducationgroup(?EducationGroup $educationgroup): self
    {
        $this->educationgroup = $educationgroup;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|StudentAbsent[]
     */
    public function getStudentAbsents(): Collection
    {
        return $this->studentAbsents;
    }

    public function addStudentAbsent(StudentAbsent $studentAbsent): self
    {
        if (!$this->studentAbsents->contains($studentAbsent)) {
            $this->studentAbsents[] = $studentAbsent;
            $studentAbsent->setSeance($this);
        }

        return $this;
    }

    public function removeStudentAbsent(StudentAbsent $studentAbsent): self
    {
        if ($this->studentAbsents->contains($studentAbsent)) {
            $this->studentAbsents->removeElement($studentAbsent);
            // set the owning side to null (unless already changed)
            if ($studentAbsent->getSeance() === $this) {
                $studentAbsent->setSeance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TeachersAbsent[]
     */
    public function getTeachersAbsents(): Collection
    {
        return $this->teachersAbsents;
    }

    public function addTeachersAbsent(TeachersAbsent $teachersAbsent): self
    {
        if (!$this->teachersAbsents->contains($teachersAbsent)) {
            $this->teachersAbsents[] = $teachersAbsent;
            $teachersAbsent->setSeance($this);
        }

        return $this;
    }

    public function removeTeachersAbsent(TeachersAbsent $teachersAbsent): self
    {
        if ($this->teachersAbsents->contains($teachersAbsent)) {
            $this->teachersAbsents->removeElement($teachersAbsent);
            // set the owning side to null (unless already changed)
            if ($teachersAbsent->getSeance() === $this) {
                $teachersAbsent->setSeance(null);
            }
        }

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->Module;
    }

    public function setModule(?Module $Module): self
    {
        $this->Module = $Module;

        return $this;
    }
    public function getDateend(): ?string
    {
        return $this->dateend;
    }

    public function setDateend(?string $dateend): self
    {
        $this->dateend = $dateend;

        return $this;
    }

    

}
