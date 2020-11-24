<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModuleDocumentRepository")
 * @Vich\Uploadable
 */
class ModuleDocument
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $supportDocument;

    /**
     * @Assert\File(maxSize="128M")
     * @Vich\UploadableField(mapping="module_supportsDocument", fileNameProperty="supportDocument")
     * @var File
     */
    private $supportDocumentFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $documentUpdatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module", inversedBy="moduleDocuments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $module;
    
    public function setSupportDocumentFile(File $supportDocument = null)
    {
        $this->supportDocumentFile = $supportDocument;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($supportDocument) {
            // if 'documentUpdatedAt' is not defined in your entity, use another property
            $this->documentUpdatedAt = new \DateTime('now');
        }
    }

    public function getSupportDocumentFile()
    {
        return $this->supportDocumentFile;
    }

    public function setSupportDocument($supportDocument)
    {
        $this->supportDocument = $supportDocument;
    }

    public function getSupportDocument()
    {
        return $this->supportDocument;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getDocumentUpdatedAt(): ?\DateTimeInterface
    {
        return $this->documentUpdatedAt;
    }

    public function setDocumentUpdatedAt(?\DateTimeInterface $documentUpdatedAt): self
    {
        $this->documentUpdatedAt = $documentUpdatedAt;

        return $this;
    }
}
