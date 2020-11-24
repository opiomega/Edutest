<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 * @Vich\Uploadable
 */
class Club
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $meatingDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $logo;
    /**
     * @Vich\UploadableField(mapping="logos", fileNameProperty="logo")
     * @var File
     */
    private $logoFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $logoUpdatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Student", inversedBy="clubsEngaged")
     */
    private $Students;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClubMessages", mappedBy="club")
     */
    private $clubMessages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="clubs")
     */
    private $head;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Events", mappedBy="club")
     */
    private $events;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Clubphoto", mappedBy="club")
     */
    private $clubphotos;

    public function __construct()
    {
        $this->Students = new ArrayCollection();
        $this->clubMessages = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->clubphotos = new ArrayCollection();
    }
    
    public function getMeatingDate(): ?\DateTimeInterface
    {
        return $this->meatingDate;
    }

    public function setMeatingDate(\DateTimeInterface $meatingDate = null): self
    {
        $this->meatingDate = $meatingDate;

        return $this;
    }

    public function setLogoFile(File $logo = null)
    {
        $this->logoFile = $logo;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($logo) {
            // if 'logoUpdatedAt' is not defined in your entity, use another property
            $this->logoUpdatedAt = new \DateTime('now');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->Students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->Students->contains($student)) {
            $this->Students[] = $student;
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->Students->contains($student)) {
            $this->Students->removeElement($student);
        }

        return $this;
    }

    /**
     * @return Collection|ClubMessages[]
     */
    public function getClubMessages(): Collection
    {
        return $this->clubMessages;
    }

    public function addClubMessage(ClubMessages $clubMessage): self
    {
        if (!$this->clubMessages->contains($clubMessage)) {
            $this->clubMessages[] = $clubMessage;
            $clubMessage->setClub($this);
        }

        return $this;
    }

    public function removeClubMessage(ClubMessages $clubMessage): self
    {
        if ($this->clubMessages->contains($clubMessage)) {
            $this->clubMessages->removeElement($clubMessage);
            // set the owning side to null (unless already changed)
            if ($clubMessage->getClub() === $this) {
                $clubMessage->setClub(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(?string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getHead(): ?Teacher
    {
        return $this->head;
    }

    public function setHead(?Teacher $head): self
    {
        $this->head = $head;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    /**
     * @return Collection|Events[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Events $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setClub($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getClub() === $this) {
                $event->setClub(null);
            }
        }

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    /**
     * @return Collection|Clubphoto[]
     */
    public function getClubphotos(): Collection
    {
        return $this->clubphotos;
    }

    public function addClubphoto(Clubphoto $clubphoto): self
    {
        if (!$this->clubphotos->contains($clubphoto)) {
            $this->clubphotos[] = $clubphoto;
            $clubphoto->setClub($this);
        }

        return $this;
    }

    public function removeClubphoto(Clubphoto $clubphoto): self
    {
        if ($this->clubphotos->contains($clubphoto)) {
            $this->clubphotos->removeElement($clubphoto);
            // set the owning side to null (unless already changed)
            if ($clubphoto->getClub() === $this) {
                $clubphoto->setClub(null);
            }
        }

        return $this;
    }
}