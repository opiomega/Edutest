<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestRepository")
 * @Vich\Uploadable
 */
class Test
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $supportPdf;

    /**
     * @Assert\File(maxSize="1G")
     * @Vich\UploadableField(mapping="test_supportsPdf", fileNameProperty="supportPdf")
     * @var File
     */
    private $supportPdfFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $pdfUpdatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="tests")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="tests")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $teacher;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="test")
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TestScore", mappedBy="test")
     */
    private $testScores;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type = "normal";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EducationGroup", inversedBy="tests")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $educationGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Classes", inversedBy="tests")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $classe;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deadline;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $listening;

    /**
     * @Assert\File(maxSize="1G")
     * @Vich\UploadableField(mapping="test_listening_audio", fileNameProperty="listening")
     * @var File
     */
    private $listeningFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $listeningUpdatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="tests")
     */
    private $topic;

    public function __construct()
    {
        $this->testScores = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function setSupportPdfFile(File $supportPdf = null)
    {
        $this->supportPdfFile = $supportPdf;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($supportPdf) {
            // if 'pdfUpdatedAt' is not defined in your entity, use another property
            $this->pdfUpdatedAt = new \DateTime('now');
        }
    }

    public function getSupportPdfFile()
    {
        return $this->supportPdfFile;
    }

    public function setSupportPdf($supportPdf)
    {
        $this->supportPdf = $supportPdf;
    }

    public function getSupportPdf()
    {
        return $this->supportPdf;
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
    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getPdfUpdatedAt(): ?\DateTimeInterface
    {
        return $this->pdfUpdatedAt;
    }

    public function setPdfUpdatedAt(?\DateTimeInterface $pdfUpdatedAt): self
    {
        $this->pdfUpdatedAt = $pdfUpdatedAt;

        return $this;
    }
    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setTest($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getTest() === $this) {
                $question->setTest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TestScore[]
     */
    public function getTestScores(): Collection
    {
        return $this->testScores;
    }

    public function addTestScore(TestScore $testScore): self
    {
        if (!$this->testScores->contains($testScore)) {
            $this->testScores[] = $testScore;
            $testScore->setTest($this);
        }

        return $this;
    }
    
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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

    public function getClasse(): ?Classes
    {
        return $this->classe;
    }

    public function setClasse(?Classes $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }
    
    public function setListeningFile(File $listening = null)
    {
        $this->listeningFile = $listening;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($listening) {
            // if 'listeningUpdatedAt' is not defined in your entity, use another property
            $this->listeningUpdatedAt = new \DateTime('now');
        }
    }

    public function getListeningFile()
    {
        return $this->listeningFile;
    }

    public function setListening($listening)
    {
        $this->listening = $listening;
    }

    public function getListening()
    {
        return $this->listening;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }
}