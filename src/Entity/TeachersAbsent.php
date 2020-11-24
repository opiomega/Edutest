<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Absence
 *
 * @ORM\Table(name="teachersAbsent")
 * @ORM\Entity(repositoryClass="App\Repository\AbsenceRepository")
 */
class TeachersAbsent
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
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seance", inversedBy="teachersAbsents")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $seance;

    
    /**
     * Absence constructor.
     */
    public function __construct()
    {
        $this->date=new \DateTime();
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
     * Set status
     *
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getTeacher()
    {
        return $this->teachers;
    }

    /**
     * @param mixed $teacher
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): self
    {
        $this->seance = $seance;

        return $this;
    }
}
