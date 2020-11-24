<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidatureDeadlineRepository")
 */
class CandidatureDeadline
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $test;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $letterOfRecommendation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $transcriptBac;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passport;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $letterOfRecommendationMath;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $transcriptThird;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $transcriptSecond;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $transcriptFirst;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $survey;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bankStatement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTest(): ?\DateTimeInterface
    {
        return $this->test;
    }

    public function setTest(?\DateTimeInterface $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getLetterOfRecommendation(): ?\DateTimeInterface
    {
        return $this->letterOfRecommendation;
    }

    public function setLetterOfRecommendation(?\DateTimeInterface $letterOfRecommendation): self
    {
        $this->letterOfRecommendation = $letterOfRecommendation;

        return $this;
    }

    public function getTranscriptBac(): ?\DateTimeInterface
    {
        return $this->transcriptBac;
    }

    public function setTranscriptBac(?\DateTimeInterface $transcriptBac): self
    {
        $this->transcriptBac = $transcriptBac;

        return $this;
    }

    public function getPassport(): ?\DateTimeInterface
    {
        return $this->passport;
    }

    public function setPassport(?\DateTimeInterface $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    public function getCin(): ?\DateTimeInterface
    {
        return $this->cin;
    }

    public function setCin(?\DateTimeInterface $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getLetterOfRecommendationMath(): ?\DateTimeInterface
    {
        return $this->letterOfRecommendationMath;
    }

    public function setLetterOfRecommendationMath(?\DateTimeInterface $letterOfRecommendationMath): self
    {
        $this->letterOfRecommendationMath = $letterOfRecommendationMath;

        return $this;
    }

    public function getTranscriptThird(): ?\DateTimeInterface
    {
        return $this->transcriptThird;
    }

    public function setTranscriptThird(?\DateTimeInterface $transcriptThird): self
    {
        $this->transcriptThird = $transcriptThird;

        return $this;
    }

    public function getTranscriptSecond(): ?\DateTimeInterface
    {
        return $this->transcriptSecond;
    }

    public function setTranscriptSecond(?\DateTimeInterface $transcriptSecond): self
    {
        $this->transcriptSecond = $transcriptSecond;

        return $this;
    }

    public function getTranscriptFirst(): ?\DateTimeInterface
    {
        return $this->transcriptFirst;
    }

    public function setTranscriptFirst(?\DateTimeInterface $transcriptFirst): self
    {
        $this->transcriptFirst = $transcriptFirst;

        return $this;
    }

    public function getSurvey(): ?\DateTimeInterface
    {
        return $this->survey;
    }

    public function setSurvey(?\DateTimeInterface $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    public function getBankStatement(): ?\DateTimeInterface
    {
        return $this->bankStatement;
    }

    public function setBankStatement(?\DateTimeInterface $bankStatement): self
    {
        $this->bankStatement = $bankStatement;

        return $this;
    }
    
    public function getAllDeadlines(){
        $allDeadlines=get_object_vars($this);
        unset($allDeadlines['id']);
        return $allDeadlines;
    }
    
    public function getUpcomingDeadline(){
        $allDeadlines = $this->getAllDeadlines();
       
       uasort($allDeadlines , function($a, $b) {
            $dateTimestamp1 = $a?strtotime($a->format('Y-m-d H:i:s')):null;
            $dateTimestamp2 = $b?strtotime($b->format('Y-m-d H:i:s')):null;

            return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
        });
      
      do {
          $document = array_key_first($allDeadlines);
          $deadline = array_shift($allDeadlines)?array_shift($allDeadlines)->format('Y-m-d H:i:s'):null;
      } while ($deadline<date('Y-m-d H:i:s'));
      /*$document = preg_replace_callback('/\b([A-Z]+)\b/', function ($word) {
      return ' '.strtolower($word[1]);
      }, array_key_first($allDeadlines));*/
        if (!$deadline)
            return "Deadlines not specified ";
        return $deadline." ".$document;
    }
}
