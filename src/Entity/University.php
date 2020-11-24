<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UniversityRepository")
 * @Vich\Uploadable
 */
class University
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $photo;
    
    /**
     * @Vich\UploadableField(mapping="universities_photos", fileNameProperty="photo")
     * @var File
     */
    private $photoFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $photoUpdatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $studentsnumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $financialaid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deadline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acceptance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Satrange;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $graduation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $employed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $earning;

    
    private $chance;

   

    

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

    public function getStudentsnumber(): ?string
    {
        return $this->studentsnumber;
    }

    public function setStudentsnumber(?string $studentsnumber): self
    {
        $this->studentsnumber = $studentsnumber;

        return $this;
    }

    public function getFinancialaid(): ?bool
    {
        return $this->financialaid;
    }

    public function setFinancialaid(bool $financialaid): self
    {
        $this->financialaid = $financialaid;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getDeadline(): ?string
    {
        return $this->deadline;
    }

    public function setDeadline(?string $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getAcceptance(): ?string
    {
        return $this->acceptance;
    }

    public function setAcceptance(?string $acceptance): self
    {
        $this->acceptance = $acceptance;

        return $this;
    }

    public function getSatrange(): ?string
    {
        return $this->Satrange;
    }

    public function setSatrange(?string $Satrange): self
    {
        $this->Satrange = $Satrange;

        return $this;
    }

    public function getGraduation(): ?string
    {
        return $this->graduation;
    }

    public function setGraduation(?string $graduation): self
    {
        $this->graduation = $graduation;

        return $this;
    }

    public function getEmployed(): ?string
    {
        return $this->employed;
    }

    public function setEmployed(?string $employed): self
    {
        $this->employed = $employed;

        return $this;
    }

    public function getEarning(): ?string
    {
        return $this->earning;
    }

    public function setEarning(?string $earning): self
    {
        $this->earning = $earning;

        return $this;
    }

    public function getChance(): ?int
    {
        return $this->chance;
    }

    public function setChance(?int $chance): self
    {
        $this->chance = $chance;

        return $this;
    }
    
}
