<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Candidature
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $test;
    
   
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $testUpdatedAt;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $letterOfRecommendation;
    
    

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $letterOfRecommendationUpdatedAt;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $transcriptBac;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $transcriptBacUpdatedAt;

    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $passport;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $passportUpdatedAt;

    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_passport", fileNameProperty="passport")
     * @var File
     */
    private $passportFile;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $cin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $cinUpdatedAt;

    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_cin", fileNameProperty="cin")
     * @var File
     */
    private $cinFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $letterOfRecommendationMath;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $letterOfRecommendationMathUpdatedAt;

    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_letterOfRecommendationsMath", fileNameProperty="letterOfRecommendationMath")
     * @var File
     */
    private $letterOfRecommendationMathFile;
    
    
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $transcriptThird;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $transcriptThirdUpdatedAt;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $transcriptSecond;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $transcriptSecondUpdatedAt;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $transcriptFirst;

    

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $transcriptFirstUpdatedAt;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $survey;

    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $surveyUpdatedAt;

    // ...

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $bankStatement;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $bankStatementUpdatedAt;
    
     /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_tests", fileNameProperty="test")
     * @var File
     */
    private $testFile;

    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_letterOfRecommendations", fileNameProperty="letterOfRecommendation")
     * @var File
     */
    private $letterOfRecommendationFile;
    
    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_transcriptsBac", fileNameProperty="transcriptBac")
     * @var File
     */
    private $transcriptBacFile;

    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_transcriptsThird", fileNameProperty="transcriptThird")
     * @var File
     */
    private $transcriptThirdFile;
    
    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_transcriptsSecond", fileNameProperty="transcriptSecond")
     * @var File
     */
    private $transcriptSecondFile;
    
    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_transcriptsFirst", fileNameProperty="transcriptFirst")
     * @var File
     */
    private $transcriptFirstFile;
    
    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_bankStatements", fileNameProperty="bankStatement")
     * @var File
     */
    private $bankStatementFile;

    /**
     * @Assert\File(maxSize="2M")
     * @Vich\UploadableField(mapping="candidature_surveys", fileNameProperty="survey")
     * @var File
     */
    private $surveyFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="candidatures")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $student;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSubmited = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="candidature", cascade={"persist"})
     */
    private $notifications;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $satScore;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }
    
    public function setTestFile(File $test = null)
    {
        $this->testFile = $test;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($test) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->testUpdatedAt = new \DateTime('now');
        }
    }

    public function getTestFile()
    {
        return $this->testFile;
    }

    public function setTest($test)
    {
        $this->test = $test;
    }

    public function getTest()
    {
        return $this->test;
    }
    
    public function setLetterOfRecommendationFile(File $letterOfRecommendation = null)
    {
        $this->letterOfRecommendationFile = $letterOfRecommendation;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($letterOfRecommendation) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->letterOfRecommendationUpdatedAt = new \DateTime('now');
        }
    }

    public function getLetterOfRecommendationFile()
    {
        return $this->letterOfRecommendationFile;
    }

    public function setLetterOfRecommendation($letterOfRecommendation)
    {
        $this->letterOfRecommendation = $letterOfRecommendation;
    }

    public function getLetterOfRecommendation()
    {
        return $this->letterOfRecommendation;
    }
    
    // ...

    public function setTranscriptBacFile(File $transcriptBac = null)
    {
        $this->transcriptBacFile = $transcriptBac;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($transcriptBac) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->transcriptBacUpdatedAt = new \DateTime('now');
        }
    }

    public function getTranscriptBacFile()
    {
        return $this->transcriptBacFile;
    }

    public function setTranscriptBac($transcriptBac)
    {
        $this->transcriptBac = $transcriptBac;
    }

    public function getTranscriptBac()
    {
        return $this->transcriptBac;
    }


     // ...

     public function setPassportFile(File $passport = null)
     {
         $this->passportFile = $passport;
 
         // VERY IMPORTANT:
         // It is required that at least one field changes if you are using Doctrine,
         // otherwise the event listeners won't be called and the file is lost
         if ($passport) {
             // if 'updatedAt' is not defined in your entity, use another property
             $this->passportUpdatedAt = new \DateTime('now');
         }
     }
 
     public function getPassportFile()
     {
         return $this->passportFile;
     }
 
     public function setPassport($passport)
     {
         $this->passport = $passport;
     }
 
     public function getPassport()
     {
         return $this->passport;
     }



     // ...

     public function setCinFile(File $cin = null)
     {
         $this->cinFile = $cin;
 
         // VERY IMPORTANT:
         // It is required that at least one field changes if you are using Doctrine,
         // otherwise the event listeners won't be called and the file is lost
         if ($cin) {
             // if 'updatedAt' is not defined in your entity, use another property
             $this->cinUpdatedAt = new \DateTime('now');
         }
     }
 
     public function getCinFile()
     {
         return $this->cinFile;
     }
 
     public function setCin($cin)
     {
         $this->cin = $cin;
     }
 
     public function getCin()
     {
         return $this->cin;
     }

    

     // ...

     public function setLetterOfRecommendationMathFile(File $letterOfRecommendationMath = null)
     {
         $this->letterOfRecommendationMathFile = $letterOfRecommendationMath;
 
         // VERY IMPORTANT:
         // It is required that at least one field changes if you are using Doctrine,
         // otherwise the event listeners won't be called and the file is lost
         if ($letterOfRecommendationMath) {
             // if 'updatedAt' is not defined in your entity, use another property
             $this->letterOfRecommendationMathUpdatedAt = new \DateTime('now');
         }
     }
 
     public function getLetterOfRecommendationMathFile()
     {
         return $this->letterOfRecommendationMathFile;
     }
 
     public function setLetterOfRecommendationMath($letterOfRecommendationMath)
     {
         $this->letterOfRecommendationMath = $letterOfRecommendationMath;
     }
 
     public function getLetterOfRecommendationMath()
     {
         return $this->letterOfRecommendationMath;
     }

    
    // ...

    public function setTranscriptThirdFile(File $transcriptThird = null)
    {
        $this->transcriptThirdFile = $transcriptThird;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($transcriptThird) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->transcriptThirdUpdatedAt = new \DateTime('now');
        }
    }

    public function getTranscriptThirdFile()
    {
        return $this->transcriptThirdFile;
    }

    public function setTranscriptThird($transcriptThird)
    {
        $this->transcriptThird = $transcriptThird;
    }

    public function getTranscriptThird()
    {
        return $this->transcriptThird;
    }
    
    // ...

    public function setTranscriptSecondFile(File $transcriptSecond = null)
    {
        $this->transcriptSecondFile = $transcriptSecond;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($transcriptSecond) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->transcriptSecondUpdatedAt = new \DateTime('now');
        }
    }

    public function getTranscriptSecondFile()
    {
        return $this->transcriptSecondFile;
    }

    public function setTranscriptSecond($transcriptSecond)
    {
        $this->transcriptSecond = $transcriptSecond;
    }

    public function getTranscriptSecond()
    {
        return $this->transcriptSecond;
    }
    
    // ...

    public function setTranscriptFirstFile(File $transcriptFirst = null)
    {
        $this->transcriptFirstFile = $transcriptFirst;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($transcriptFirst) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->transcriptFirstUpdatedAt = new \DateTime('now');
        }
    }

    public function getTranscriptFirstFile()
    {
        return $this->transcriptFirstFile;
    }

    public function setTranscriptFirst($transcriptFirst)
    {
        $this->transcriptFirst = $transcriptFirst;
    }

    public function getTranscriptFirst()
    {
        return $this->transcriptFirst;
    }
    
    public function setSurveyFile(File $survey = null)
    {
        $this->surveyFile = $survey;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($survey) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->surveyUpdatedAt = new \DateTime('now');
        }
    }

    public function getSurveyFile()
    {
        return $this->surveyFile;
    }

    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    public function getSurvey()
    {
        return $this->survey;
    }

    public function setBankStatementFile(File $bankStatement = null)
    {
        $this->bankStatementFile = $bankStatement;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($bankStatement) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->bankStatementUpdatedAt = new \DateTime('now');
        }
    }

    public function getBankStatementFile()
    {
        return $this->bankStatementFile;
    }

    public function setBankStatement($bankStatement)
    {
        $this->bankStatement = $bankStatement;
    }

    public function getBankStatement()
    {
        return $this->bankStatement;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
    public function getAllFieldsFull() {
	$ov = get_object_vars($this);
            if (!strpos(array_search(null,$ov),'File'))
                return false;
	return true;
    }

    public function getIsSubmited(): ?bool
    {
        return $this->isSubmited;
    }

    public function setIsSubmited(bool $isSubmited): self
    {
        $this->isSubmited = $isSubmited;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setCandidature($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getCandidature() === $this) {
                $notification->setCandidature(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSatScore(): ?int
    {
        return $this->satScore;
    }

    public function setSatScore(?int $satScore): self
    {
        $this->satScore = $satScore;

        return $this;
    }
    public function getAllDocuments(){
        $allDocuments=get_object_vars($this);
        unset($allDocuments['id'],$allDocuments['student'],$allDocuments['isSubmited'],$allDocuments['notification'],$allDocuments['username'],$allDocuments['Password'],$allDocuments['satScore']);
        return $allDocuments;
    }
}
