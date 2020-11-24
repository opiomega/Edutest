<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CounslingRepository")
 */
class Counsling
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
    private $begintime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $endtime;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="counslings")
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="counslings")
     */
    private $Student;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EducationGroup", inversedBy="counslings")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $educationgroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Days", inversedBy="counslings")
     */
    private $day;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $topic;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $medium;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBegintime(): ?string
    {
        return $this->begintime;
    }

    public function setBegintime(string $begintime): self
    {
        $this->begintime = $begintime;

        return $this;
    }

    public function getEndtime(): ?string
    {
        return $this->endtime;
    }

    public function setEndtime(string $endtime): self
    {
        $this->endtime = $endtime;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getStudent(): ?Student
    {
        return $this->Student;
    }

    public function setStudent(?Student $Student): self
    {
        $this->Student = $Student;

        return $this;
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

    public function getDay(): ?Days
    {
        return $this->day;
    }

    public function setDay(?Days $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(?string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getMedium(): ?string
    {
        return $this->medium;
    }

    public function setMedium(?string $medium): self
    {
        $this->medium = $medium;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
