<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Test;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/index/{testId}", name="question_index", methods={"GET"})
     */
    public function index(QuestionRepository $questionRepository,TestRepository $testRepository, $testId): Response
    {
        $test = $testRepository->find($testId);
        return $this->render('question/index.html.twig', [
            'questions' => $questionRepository->findBy(['test'=>$testId]),
            'test' => $test,
        ]);
    }

    /**
     * @Route("/new/{id}", name="question_new", methods={"GET","POST"})
     */
    public function new(Request $request, Test $test): Response
    {
        if ($this->getUser()->getStatus()!="Admin"){
            $this->addFlash("danger", "This user is not admin");
            return $this->redirectToRoute('test_selectCategory');
        }
        $question = new Question();
        $question->setTest($test);
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('question_index',['testId'=>$test->getId()]);
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="question_show", methods={"GET"})
     */
    public function show(Question $question): Response
    {
        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Question $question): Response
    {
        if ($this->getUser()->getStatus()!="Admin"){
            $this->addFlash("danger", "This user is not admin");
            return $this->redirectToRoute('test_selectCategory');
        }
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choises = [];
            foreach ($question->getChoises() as $choise) {
               if ($choise != ""){
                   $choises[]=$choise;
               }
            }
            $question->setChoises($choises);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('question_index',["testId" => $question->getTest()->getId()]);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="question_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Question $question): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->getUser()->getStatus()!="Admin"){
            $this->addFlash("danger", "This user is not admin");
            return $this->redirectToRoute('test_selectCategory');
        }
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_index',["testId" => $question->getTest()->getId()]);
    }
}
