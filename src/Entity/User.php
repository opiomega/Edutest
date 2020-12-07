<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;



/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @Assert\Length(
     *      min = 8,
     *      minMessage = "Your first name must be at least 8 characters long"
     * )
     * @ORM\Column(type="string", length=255)  
     */
    private $password;
    
    /**
     * @ORM\Column(type="integer")  
     */
    private $active;
    
    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     * @var string
     */
    private $photo;
    
    /**
     * @Vich\UploadableField(mapping="user_photos", fileNameProperty="photo")
     * @var File
     */
    private $photoFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $photoUpdatedAt;


    

    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }
    
    /**
     * @see UserInterface
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;
    
     /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;
     /**
     * @ORM\Column(type="string", length=255)
     */
    private $adress;
      /**
    * @ORM\Column(name="datebirth", type="string")
    */
    private $datebirth;


   

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

     /**
     * @return string
     */
    public function getDatebirth()
    {
        return $this->datebirth;
    }

    /**
     * @param string $datebirth
     */
    public function setDatebirth($datebirth)
    {
        $this->datebirth = $datebirth;
    }
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $zipcode;

    

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode($zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }
   
    public function __toString() {
        return $this->email;
    }
    
     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="user")
     * 
     */
    private $student;

     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Teacher", mappedBy="user")
     */
    private $teacher;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastlogin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Messages", mappedBy="user")
     */
    private $messages;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $citybirth;

    public function __construct()
    {
        $this->student = new ArrayCollection();
        $this->teacher = new ArrayCollection();
        $this->messages = new ArrayCollection();
       
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudent(): Collection
    {
        return $this->student;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->student->contains($student)) {
            $this->student[] = $student;
            $student->setUser($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->student->contains($student)) {
            $this->student->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getUser() === $this) {
                $student->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Teacher[]
     */
    public function getTeacher(): Collection
    {
        return $this->teacher;
    }

    public function addTeacher(Teacher $teacher): self
    {
        if (!$this->teacher->contains($teacher)) {
            $this->teacher[] = $teacher;
            $teacher->setUser($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): self
    {
        if ($this->teacher->contains($teacher)) {
            $this->teacher->removeElement($teacher);
            // set the owning side to null (unless already changed)
            if ($teacher->getUser() === $this) {
                $teacher->setUser(null);
            }
        }

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

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

    

    public function getLastlogin(): ?\DateTimeInterface
    {
        return $this->lastlogin;
    }

    public function setLastlogin(?\DateTimeInterface $lastlogin): self
    {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * @return Collection|Messages[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }
    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getCitybirth(): ?string
    {
        return $this->citybirth;
    }

    public function setCitybirth(string $citybirth): self
    {
        $this->citybirth = $citybirth;

        return $this;
    }
}
