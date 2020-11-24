<?php

namespace App\Controller;

use App\Entity\University;
use App\Form\UniversityType;
use App\Repository\UniversityRepository;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
/**
 * @Route("/university")
 */
class UniversityController extends AbstractController
{
    /**
     * @Route("/", name="university_index", methods={"GET"})
     */
    public function index(UniversityRepository $universityRepository): Response
    {
        return $this->render('university/index.html.twig', [
            'universities' => $universityRepository->findAll(),
        ]);
    }


     /**
     * @Route("/student", name="univ_student_index", methods={"GET"})
     */
    public function univindex(UniversityRepository $universityRepository,CandidatureRepository $candidatureRepository): Response
    {
        $student=$this->getUser()->getStudent();
        $studentid=$student[0]->getId();
        $candidature=$candidatureRepository->findBy(['student'=>$studentid]);
        $new=$universityRepository->findBy(array(),array('id'=>'desc'));
        return $this->render('university/universities_student.html.twig', [
            'universities' => $universityRepository->findAll(),
            'new'=>$new,
            'candidature'=>$candidature
        ]);
    }


    /**
     * @Route("/new", name="university_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $university = new University();
        $form = $this->createForm(UniversityType::class, $university);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($university);
            $entityManager->flush();
            $this->addFlash('success', 'University Created! Knowledge is power!');
            return $this->redirectToRoute('university_index');
        }

        return $this->render('university/new.html.twig', [
            'university' => $university,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="university_show", methods={"GET"})
     */
    public function show(University $university): Response
    {
        return $this->render('university/show.html.twig', [
            'university' => $university,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="university_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, University $university): Response
    {
        $form = $this->createForm(UniversityType::class, $university);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute('university_index');
        }

        return $this->render('university/edit.html.twig', [
            'university' => $university,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="university_delete", methods={"DELETE"})
     */
    public function delete(Request $request, University $university): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$university->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($university);
            $entityManager->flush();
            $this->addFlash("delete", "University deleted ");
        }

        return $this->redirectToRoute('university_index');
    }
}
