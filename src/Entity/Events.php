<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventsRepository")
 * @Vich\Uploadable
 */
class Events
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="events")
     */
    private $club;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $photo;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $photoUpdatedAt;

    /**
     * @Vich\UploadableField(mapping="events", fileNameProperty="photo")
     * @var File
     */
    private $photoFile;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

     // ...

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
}
