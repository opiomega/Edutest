<?php

namespace App\Controller;

use App\Entity\Educationlevel;
use App\Form\EducationlevelType;
use App\Repository\EducationlevelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/educationlevel")
 */
class EducationlevelController extends AbstractController
{
    /**
     * @Route("/", name="educationlevel_index", methods={"GET","POST"})
     */
    public function index(EducationlevelRepository $educationlevelRepository,Request $request): Response
    {
        $educationlevel = new Educationlevel();
        $form = $this->createForm(EducationlevelType::class, $educationlevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($educationlevel);
            $entityManager->flush();
            $this->addFlash("success", "New Educationlevel created ");

            return $this->redirectToRoute('educationlevel_index');
        }
        return $this->render('educationlevel/index.html.twig', [
            'educationlevels' => $educationlevelRepository->findAll(),
            'educationlevel' => $educationlevel,
            
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="educationlevel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $educationlevel = new Educationlevel();
        $form = $this->createForm(EducationlevelType::class, $educationlevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($educationlevel);
            $entityManager->flush();
            $this->addFlash("success", "New education created ");

            return $this->redirectToRoute('educationlevel_index');
        }

        return $this->render('educationlevel/new.html.twig', [
            'educationlevel' => $educationlevel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="educationlevel_show", methods={"GET"})
     */
    public function show(Educationlevel $educationlevel): Response
    {
        return $this->render('educationlevel/show.html.twig', [
            'educationlevel' => $educationlevel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="educationlevel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Educationlevel $educationlevel): Response
    {


        $name=$request->get('name');
        $educationlevel->setName($name);
        $this->getDoctrine()->getManager()->persist($educationlevel);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("update", "Informations updated ");
        return $this->redirectToRoute('educationlevel_index');

    }

    /**
     * @Route("/{id}", name="educationlevel_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Educationlevel $educationlevel): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$educationlevel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($educationlevel);
            $entityManager->flush();
            $this->addFlash("delete", "Education level deleted ");
        }

        return $this->redirectToRoute('educationlevel_index');
    }
}
