<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ClubphotoRepository")
 * @Vich\Uploadable
 */
class Clubphoto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="clubphotos")
     */
    private $club;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $photoc;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $photocUpdatedAt;

    /**
     * @Vich\UploadableField(mapping="photoc", fileNameProperty="photoc")
     * @var File
     */
    private $photocFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eventname;


    public function getId(): ?int
    {
        return $this->id;
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

    public function setPhotocFile(File $photoc = null)
    {
        $this->photocFile = $photoc;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($photoc) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->photocUpdatedAt = new \DateTime('now');
        }
    }

    public function getPhotocFile()
    {
        return $this->photocFile;
    }

    public function setPhotoc($photoc)
    {
        $this->photoc = $photoc;
    }

    public function getPhotoc()
    {
        return $this->photoc;
    }

    public function getEventname(): ?string
    {
        return $this->eventname;
    }

    public function setEventname(string $eventname): self
    {
        $this->eventname = $eventname;

        return $this;
    }
}
