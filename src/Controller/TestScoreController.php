<?php

namespace App\Controller;

use App\Entity\TestScore;
use App\Form\TestScoreType;
use App\Repository\TestScoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test/score")
 */
class TestScoreController extends AbstractController
{
    /**
     * @Route("/", name="test_score_index", methods={"GET"})
     */
    public function index(TestScoreRepository $testScoreRepository): Response
    {
        return $this->render('test_score/index.html.twig', [
            'test_scores' => $testScoreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="test_score_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $testScore = new TestScore();
        $form = $this->createForm(TestScoreType::class, $testScore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($testScore);
            $entityManager->flush();

            return $this->redirectToRoute('test_score_index');
        }

        return $this->render('test_score/new.html.twig', [
            'test_score' => $testScore,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="test_score_show", methods={"GET"})
     */
    public function show(TestScore $testScore): Response
    {
        return $this->render('test_score/show.html.twig', [
            'test_score' => $testScore,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="test_score_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TestScore $testScore): Response
    {
        $form = $this->createForm(TestScoreType::class, $testScore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('test_score_index');
        }

        return $this->render('test_score/edit.html.twig', [
            'test_score' => $testScore,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="test_score_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TestScore $testScore): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$testScore->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($testScore);
            $entityManager->flush();
        }

        return $this->redirectToRoute('test_score_index');
    }
}
