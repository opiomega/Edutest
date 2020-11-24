<?php

namespace App\Controller;

use App\Entity\CandidatureDeadline;
use App\Form\CandidatureDeadlineType;
use App\Repository\CandidatureDeadlineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/applications/candidature/deadline")
 */
class CandidatureDeadlineController extends AbstractController
{
    /**
     * @Route("/", name="candidature_deadline_index", methods={"GET"})
     */
    public function index(CandidatureDeadlineRepository $candidatureDeadlineRepository): Response
    {
        return $this->render('candidature_deadline/index.html.twig', [
            'candidature_deadlines' => $candidatureDeadlineRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="candidature_deadline_new", methods={"GET","POST"})
     */
    public function new(Request $request,CandidatureDeadlineRepository $candidatureDeadlineRepository): Response
    {
        $candidatureDeadline = $candidatureDeadlineRepository->findOneBy([],['id'=>'DESC'],0,1);
        if ($candidatureDeadline!=null){
            return $this->redirectToRoute('candidature_deadline_edit',['id'=>$candidatureDeadline->getId()]);
        }
        $candidatureDeadline = new CandidatureDeadline();
        $form = $this->createForm(CandidatureDeadlineType::class, $candidatureDeadline);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($candidatureDeadline);
            $entityManager->flush();

            return $this->redirectToRoute('candidature_deadline_edit',['id'=>$candidatureDeadline->getId()]);
        }

        return $this->render('candidature_deadline/new.html.twig', [
            'candidature_deadline' => $candidatureDeadline,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="candidature_deadline_show", methods={"GET"})
     */
    public function show(CandidatureDeadline $candidatureDeadline): Response
    {
        return $this->render('candidature_deadline/show.html.twig', [
            'candidature_deadline' => $candidatureDeadline,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="candidature_deadline_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CandidatureDeadline $candidatureDeadline): Response
    {
        $form = $this->createForm(CandidatureDeadlineType::class, $candidatureDeadline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('candidature_deadline/edit.html.twig', [
            'candidature_deadline' => $candidatureDeadline,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="candidature_deadline_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CandidatureDeadline $candidatureDeadline): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$candidatureDeadline->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidatureDeadline);
            $entityManager->flush();
        }

        return $this->redirectToRoute('candidature_deadline_new');
    }
}
