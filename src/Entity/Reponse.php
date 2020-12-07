<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReponseRepository::class)
 */
class Reponse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Test::class, inversedBy="responses")
     */
    private $test;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="responses")
     */
    private $question;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $response;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reponsemath;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="reponses")
     */
    private $student;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getResponse(): ?int
    {
        return $this->response;
    }

    public function setResponse(?int $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getReponsemath(): ?string
    {
        return $this->reponsemath;
    }

    public function setReponsemath(?string $reponsemath): self
    {
        $this->reponsemath = $reponsemath;

        return $this;
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
}
