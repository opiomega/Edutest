<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $Content;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $choises = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $correctChoise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Test", inversedBy="questions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $test;

    

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $answer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $correctAnswer;

    
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $accur = [];


    public function __construct()
    {
        $this->notes = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): self
    {
        $this->Content = $Content;

        return $this;
    }

    public function getChoises(): ?array
    {
        return $this->choises;
    }

    public function setChoises(array $choises): self
    {
        $this->choises = $choises;

        return $this;
    }

    public function getCorrectChoise(): ?int
    {
        return $this->correctChoise;
    }

    public function setCorrectChoise(int $correctChoise): self
    {
        $this->correctChoise = $correctChoise;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): self
    {
        $this->test = $test;

        return $this;
    }

    

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCorrectAnswer(): ?string
    {
        return $this->correctAnswer;
    }

    public function setCorrectAnswer(?string $correctAnswer): self
    {
        $this->correctAnswer = $correctAnswer;

        return $this;
    }
    public function getAccur(): ?array
    {
        return $this->accur;
    }

    public function setAccur(array $accur): self
    {
        $this->accur = $accur;

        return $this;
    }
    
}
