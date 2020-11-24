<?php

namespace App\Controller;

use App\Entity\Hearaboutus;
use App\Form\HearaboutusType;
use App\Repository\HearaboutusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hearaboutus")
 */
class HearaboutusController extends AbstractController
{
    /**
     * @Route("/", name="hearaboutus_index", methods={"GET","POST"})
     */
    public function index(HearaboutusRepository $hearaboutusRepository,Request $request): Response
    {   
        $hearaboutus = new Hearaboutus();
        $form = $this->createForm(HearaboutusType::class, $hearaboutus);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hearaboutus);
            $entityManager->flush();
            $this->addFlash("success", "New category created ");

            return $this->redirectToRoute('hearaboutus_index');
        }
        return $this->render('hearaboutus/index.html.twig', [
            'hearaboutuses' => $hearaboutusRepository->findAll(),
            'hearaboutus'=>$hearaboutus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="hearaboutus_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $hearaboutus = new Hearaboutus();
        $form = $this->createForm(HearaboutusType::class, $hearaboutus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hearaboutus);
            $entityManager->flush();
            $this->addFlash("success", "Source created ");
            return $this->redirectToRoute('hearaboutus_index');
        }

        return $this->render('hearaboutus/new.html.twig', [
            'hearaboutus' => $hearaboutus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hearaboutus_show", methods={"GET"})
     */
    public function show(Hearaboutus $hearaboutus): Response
    {
        return $this->render('hearaboutus/show.html.twig', [
            'hearaboutus' => $hearaboutus,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="hearaboutus_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Hearaboutus $hearaboutus): Response
    {
        $name=$request->get('name');
        $hearaboutus->setName($name);
        $this->getDoctrine()->getManager()->persist($hearaboutus);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("update", "Informations updated ");
        return $this->redirectToRoute('hearaboutus_index');

        
    }

    /**
     * @Route("/{id}", name="hearaboutus_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Hearaboutus $hearaboutus): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$hearaboutus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hearaboutus);
            $entityManager->flush();
            $this->addFlash("delete", "Source deleted ");
        }

        return $this->redirectToRoute('hearaboutus_index');
    }
}
