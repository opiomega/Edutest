<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
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
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $school;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstparentname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstparentjob;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secondparentname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secondparentjob;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fristparentemail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstparentnumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secondparentemail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secondparentnumbre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $membretalk;

    /**
     * @ORM\Column(type="boolean")
     */
    private $levelTest = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSchool(): ?string
    {
        return $this->school;
    }

    public function setSchool(string $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getFirstparentname(): ?string
    {
        return $this->firstparentname;
    }

    public function setFirstparentname(string $firstparentname): self
    {
        $this->firstparentname = $firstparentname;

        return $this;
    }

    public function getFirstparentjob(): ?string
    {
        return $this->firstparentjob;
    }

    public function setFirstparentjob(string $firstparentjob): self
    {
        $this->firstparentjob = $firstparentjob;

        return $this;
    }

    public function getSecondparentname(): ?string
    {
        return $this->secondparentname;
    }

    public function setSecondparentname(string $secondparentname): self
    {
        $this->secondparentname = $secondparentname;

        return $this;
    }

    public function getSecondparentjob(): ?string
    {
        return $this->secondparentjob;
    }

    public function setSecondparentjob(string $secondparentjob): self
    {
        $this->secondparentjob = $secondparentjob;

        return $this;
    }

    public function getFristparentemail(): ?string
    {
        return $this->fristparentemail;
    }

    public function setFristparentemail(string $fristparentemail): self
    {
        $this->fristparentemail = $fristparentemail;

        return $this;
    }

    public function getFirstparentnumber(): ?string
    {
        return $this->firstparentnumber;
    }

    public function setFirstparentnumber(string $firstparentnumber): self
    {
        $this->firstparentnumber = $firstparentnumber;

        return $this;
    }

    public function getSecondparentemail(): ?string
    {
        return $this->secondparentemail;
    }

    public function setSecondparentemail(string $secondparentemail): self
    {
        $this->secondparentemail = $secondparentemail;

        return $this;
    }

    public function getSecondparentnumbre(): ?string
    {
        return $this->secondparentnumbre;
    }

    public function setSecondparentnumbre(string $secondparentnumbre): self
    {
        $this->secondparentnumbre = $secondparentnumbre;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getMembretalk(): ?string
    {
        return $this->membretalk;
    }

    public function setMembretalk(string $membretalk): self
    {
        $this->membretalk = $membretalk;

        return $this;
    }
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Classes", inversedBy="students")
     */
    private $classes;

    public function getClasses(): ?Classes
    {
        return $this->classes;
    }

    public function setClasses(?Classes $classes): self
    {
        $this->classes = $classes;

        return $this;
    }


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Educationlevel", inversedBy="students")
     */
    private $educationlevel;

    public function getEducationlevel(): ?Educationlevel
    {
        return $this->educationlevel;
    }

    public function setEducationlevel(?Educationlevel $educationlevel): self
    {
        $this->educationlevel = $educationlevel;

        return $this;
    }
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hearaboutus", inversedBy="students")
     */
    private $hearaboutus;

    public function getHearaboutus(): ?Hearaboutus
    {
        return $this->hearaboutus;
    }

    public function setHearaboutus(?Hearaboutus $hearaboutus): self
    {
        $this->hearaboutus = $hearaboutus;

        return $this;
    }
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     *@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher")
     *@ORM\JoinColumn(name="teacher_id", referencedColumnName="id")
     */
    private $teacher;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Candidature", mappedBy="student")
     */
    private $candidatures;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="transmitter")
     */
    private $notifications;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $paymentday;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $nextpaymentday;
    /**
     * @ORM\ManyToOne(targetEntity="Classes")
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     */
    private $classe;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TestScore", mappedBy="student")
     */
    private $testScore;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="students")
     */
    private $levelTestType;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Club", mappedBy="Students")
     */
    private $clubsEngaged;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClubMessages", mappedBy="student")
     */
    private $clubMessages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hobbies;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $achievement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $specialskills;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\University", inversedBy="students")
     */
    private $universities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Application", mappedBy="student")
     */
    private $applications;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EducationGroup", mappedBy="students")
     */
    private $educationGroups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Counsling", mappedBy="Student")
     */
    private $counslings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentConsling", mappedBy="student")
     */
    private $studentConslings;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdat;

    /**
     * @ORM\Column(type="boolean")
     */
    private $term;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $online;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $schoollocation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $travelabroad;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $travelreason;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Courses", inversedBy="students")
     */
    private $courses;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parentspayeducation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $payeducation;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $membership;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $enrollededutest;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $preferredlanguage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $organisationmembership;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->testScore = new ArrayCollection();
        $this->clubsEngaged = new ArrayCollection();
        $this->universities = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->educationGroups = new ArrayCollection();
        $this->counslings = new ArrayCollection();
        $this->studentConslings = new ArrayCollection();
        $this->courses = new ArrayCollection();
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection|Candidature[]
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures[] = $candidature;
            $candidature->setStudent($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->contains($candidature)) {
            $this->candidatures->removeElement($candidature);
            // set the owning side to null (unless already changed)
            if ($candidature->getStudent() === $this) {
                $candidature->setStudent(null);
            }
        }

        return $this;
    }

    public function getPaymentday(): ?\DateTimeInterface
    {
        return $this->paymentday;
    }

    public function setPaymentday(?\DateTimeInterface $paymentday): self
    {
        $this->paymentday = $paymentday;

        return $this;
    }

    public function getNextpaymentday(): ?\DateTimeInterface
    {
        return $this->nextpaymentday;
    }

    public function setNextpaymentday(?\DateTimeInterface $nextpaymentday): self
    {
        $this->nextpaymentday = $nextpaymentday;

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
            $notification->setTransmitter($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getTransmitter() === $this) {
                $notification->setTransmitter(null);
            }
        }

        return $this;
    }


    /**
     * @return mixed
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * @param mixed $classe
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;
    }
    /**
     * @return Collection|TestScore[]
     */
    public function getTestScore(): Collection
    {
        return $this->testScore;
    }

    public function addTestScore(TestScore $testScore): self
    {
        if (!$this->testScore->contains($testScore)) {
            $this->testScore[] = $testScore;
            $testScore->setStudent($this);
        }

        return $this;
    }

    public function removeTestScore(TestScore $testScore): self
    {
        if ($this->testScore->contains($testScore)) {
            $this->testScore->removeElement($testScore);
            // set the owning side to null (unless already changed)
            if ($testScore->getStudent() === $this) {
                $testScore->setStudent(null);
            }
        }

        return $this;
    }
    public function getLevelTest(): ?bool
    {
        return $this->levelTest;
    }

    public function setLevelTest($levelTest): self
    {
        $this->levelTest = $levelTest;

        return $this;
    }

    public function getLevelTestType(): ?Category
    {
        return $this->levelTestType;
    }

    public function setLevelTestType(?Category $levelTestType): self
    {
        $this->levelTestType = $levelTestType;

        return $this;
    }

    /**
     * @return Collection|Club[]
     */
    public function getClubsEngaged(): Collection
    {
        return $this->clubsEngaged;
    }

    public function addClubsEngaged(Club $clubsEngaged): self
    {
        if (!$this->clubsEngaged->contains($clubsEngaged)) {
            $this->clubsEngaged[] = $clubsEngaged;
            $clubsEngaged->addStudent($this);
        }

        return $this;
    }

    public function removeClubsEngaged(Club $clubsEngaged): self
    {
        if ($this->clubsEngaged->contains($clubsEngaged)) {
            $this->clubsEngaged->removeElement($clubsEngaged);
            $clubsEngaged->removeStudent($this);
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
            $clubMessage->setStudent($this);
        }

        return $this;
    }

    public function removeClubMessage(ClubMessages $clubMessage): self
    {
        if ($this->clubMessages->contains($clubMessage)) {
            $this->clubMessages->removeElement($clubMessage);
            // set the owning side to null (unless already changed)
            if ($clubMessage->getStudent() === $this) {
                $clubMessage->setStudent(null);
            }
        }

        return $this;
    }

    public function getHobbies(): ?string
    {
        return $this->hobbies;
    }

    public function setHobbies(?string $hobbies): self
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    public function getAchievement(): ?string
    {
        return $this->achievement;
    }

    public function setAchievement(?string $achievement): self
    {
        $this->achievement = $achievement;

        return $this;
    }

    public function getSpecialskills(): ?string
    {
        return $this->specialskills;
    }

    public function setSpecialskills(?string $specialskills): self
    {
        $this->specialskills = $specialskills;

        return $this;
    }

    /**
     * @return Collection|University[]
     */
    public function getUniversities(): Collection
    {
        return $this->universities;
    }

    public function addUniversity(University $university): self
    {
        if (!$this->universities->contains($university)) {
            $this->universities[] = $university;
        }

        return $this;
    }

    public function removeUniversity(University $university): self
    {
        if ($this->universities->contains($university)) {
            $this->universities->removeElement($university);
        }

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setStudent($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
            // set the owning side to null (unless already changed)
            if ($application->getStudent() === $this) {
                $application->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EducationGroup[]
     */
    public function getEducationGroups(): Collection
    {
        return $this->educationGroups;
    }

    public function addEducationGroup(EducationGroup $educationGroup): self
    {
        if (!$this->educationGroups->contains($educationGroup)) {
            $this->educationGroups[] = $educationGroup;
        }

        return $this;
    }

    public function removeEducationGroup(EducationGroup $educationGroup): self
    {
        if ($this->educationGroups->contains($educationGroup)) {
            $this->educationGroups->removeElement($educationGroup);
        }

        return $this;
    }

    public function getFirstAndLstname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->createdat;
    }

    public function setCreatedat(?\DateTimeInterface $createdat): self
    {
        $this->createdat = $createdat;

        return $this;
    }

    public function getTerm(): ?bool
    {
        return $this->term;
    }

    public function setTerm(bool $term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return Collection|Counsling[]
     */
    public function getCounslings(): Collection
    {
        return $this->counslings;
    }

    public function addCounsling(Counsling $counsling): self
    {
        if (!$this->counslings->contains($counsling)) {
            $this->counslings[] = $counsling;
            $counsling->setStudent($this);
        }

        return $this;
    }

    public function removeCounsling(Counsling $counsling): self
    {
        if ($this->counslings->contains($counsling)) {
            $this->counslings->removeElement($counsling);
            // set the owning side to null (unless already changed)
            if ($counsling->getStudent() === $this) {
                $counsling->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StudentConsling[]
     */
    public function getStudentConslings(): Collection
    {
        return $this->studentConslings;
    }

    public function addStudentConsling(StudentConsling $studentConsling): self
    {
        if (!$this->studentConslings->contains($studentConsling)) {
            $this->studentConslings[] = $studentConsling;
            $studentConsling->setStudent($this);
        }

        return $this;
    }

    public function removeStudentConsling(StudentConsling $studentConsling): self
    {
        if ($this->studentConslings->contains($studentConsling)) {
            $this->studentConslings->removeElement($studentConsling);
            // set the owning side to null (unless already changed)
            if ($studentConsling->getStudent() === $this) {
                $studentConsling->setStudent(null);
            }
        }

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(?bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getSchoollocation(): ?string
    {
        return $this->schoollocation;
    }

    public function setSchoollocation(string $schoollocation): self
    {
        $this->schoollocation = $schoollocation;

        return $this;
    }

    public function getTravelabroad(): ?string
    {
        return $this->travelabroad;
    }

    public function setTravelabroad(string $travelabroad): self
    {
        $this->travelabroad = $travelabroad;

        return $this;
    }

    public function getTravelReason(): ?string
    {
        return $this->travelreason;
    }

    public function setTravelReason(string $travelreason): self
    {
        $this->Travelreason = $travelreason;

        return $this;
    }

    /**
     * @return Collection|Courses[]
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourses(Courses $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
        }

        return $this;
    }

    public function removeCourses(Courses $course): self
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
        }

        return $this;
    }

    public function getParentspayeducation(): ?string
    {
        return $this->parentspayeducation;
    }

    public function setParentspayeducation(string $parentspayeducation): self
    {
        $this->parentspayeducation = $parentspayeducation;

        return $this;
    }

    public function getPayeducation(): ?string
    {
        return $this->payeducation;
    }

    public function setPayeducation(string $payeducation): self
    {
        $this->payeducation = $payeducation;

        return $this;
    }


    public function getMembership(): ?string
    {
        return $this->membership;
    }

    public function setMembership(string $membership): self
    {
        $this->membership = $membership;

        return $this;
    }

    public function getEnrollededutest(): ?string
    {
        return $this->enrollededutest;
    }

    public function setEnrollededutest(string $enrollededutest): self
    {
        $this->enrollededutest = $enrollededutest;

        return $this;
    }

    public function getPreferredlanguage(): ?string
    {
        return $this->preferredlanguage;
    }

    public function setPreferredlanguage(string $preferredlanguage): self
    {
        $this->preferredlanguage = $preferredlanguage;

        return $this;
    }

    public function getOrganisationmembership(): ?string
    {
        return $this->organisationmembership;
    }

    public function setOrganisationmembership(string $organisationmembership): self
    {
        $this->organisationmembership = $organisationmembership;

        return $this;
    }
}
