<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModuleRepository")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 */
class Module
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
     */
    private $video;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $iframe;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeCourse", inversedBy="modules")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $TypeCourse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $supportPdf;

    /**
     * @Vich\UploadableField(mapping="module_supportsPdf", fileNameProperty="supportPdf")
     * @var File
     */
    private $supportPdfFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $pdfUpdatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="Module")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="modules")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $teacher;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ModuleDocument", mappedBy="module")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $moduleDocuments;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EducationGroup", inversedBy="module")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $educationGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Classes", inversedBy="modules")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $classe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Seance", mappedBy="Module")
     */
    private $seances;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="modules")
     */
    private $Topic;
   

    

    public function __construct()
    {
        $this->moduleDocuments = new ArrayCollection();
        $this->seances = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getIframe(): ?string
    {
        return $this->iframe;
    }

    public function setIframe(?string $iframe): self
    {
        $this->iframe = $iframe;

        return $this;
    }

    public function getTypeCourse(): ?TypeCourse
    {
        return $this->TypeCourse;
    }

    public function setTypeCourse(?TypeCourse $TypeCourse): self
    {
        $this->TypeCourse = $TypeCourse;

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
    
    public function __toString() {
        return (string)$this->id;
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
    
    /**
     * @return Collection|ModuleDocument[]
     */
    public function getModuleDocuments(): Collection
    {
        return $this->moduleDocuments;
    }

    public function addModuleDocument(ModuleDocument $moduleDocument): self
    {
        if (!$this->moduleDocuments->contains($moduleDocument)) {
            $this->moduleDocuments[] = $moduleDocument;
            $moduleDocument->setModule($this);
        }

        return $this;
    }
    public function removeModuleDocument(ModuleDocument $moduleDocument): self
    {
        if ($this->moduleDocuments->contains($moduleDocument)) {
            $this->moduleDocuments->removeElement($moduleDocument);
            // set the owning side to null (unless already changed)
            if ($moduleDocument->getModule() === $this) {
                $moduleDocument->setModule(null);
            }
        }

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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
            $seance->setModule($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        if ($this->seances->contains($seance)) {
            $this->seances->removeElement($seance);
            // set the owning side to null (unless already changed)
            if ($seance->getModule() === $this) {
                $seance->setModule(null);
            }
        }

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->Topic;
    }

    public function setTopic(?Topic $Topic): self
    {
        $this->Topic = $Topic;

        return $this;
    }
}
