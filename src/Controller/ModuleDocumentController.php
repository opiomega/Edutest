<?php

namespace App\Controller;

use App\Entity\ModuleDocument;
use App\Entity\Module;
use App\Form\ModuleDocumentType;
use App\Repository\ModuleDocumentRepository;
use App\Repository\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/module/document")
 */
class ModuleDocumentController extends AbstractController
{
    /**
     * @Route("/index/{moduleId}", name="module_document_index", methods={"GET"})
     */
    public function index(ModuleDocumentRepository $moduleDocumentRepository, ModuleRepository $moduleReposetory,$moduleId): Response
    {
        $module = $moduleReposetory->find($moduleId);
        return $this->render('module_document/index.html.twig', [
            'module_documents' => $moduleDocumentRepository->findBy(["module"=>$moduleId]),
            'module' => $module,
        ]);
    }

    /**
     * @Route("/new/{id}", name="module_document_new", methods={"GET","POST"})
     */
    public function new(Request $request,Module $module): Response
    {
        $moduleDocument = new ModuleDocument();
        $moduleDocument->setModule($module);
        $form = $this->createForm(ModuleDocumentType::class, $moduleDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($moduleDocument);
            $entityManager->flush();
            $this->addFlash("success", "New document created ");

            return $this->redirectToRoute('module_document_index',['moduleId'=>$module->getId()]);
        }

        return $this->render('module_document/new.html.twig', [
            'module_document' => $moduleDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="module_document_show", methods={"GET"})
     */
    public function show(ModuleDocument $moduleDocument): Response
    {
        return $this->render('module_document/show.html.twig', [
            'module_document' => $moduleDocument,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="module_document_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ModuleDocument $moduleDocument): Response
    {
        $form = $this->createForm(ModuleDocumentType::class, $moduleDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute('module_document_index',['moduleId'=>$moduleDocument->getModule()->getId()]);
        }

        return $this->render('module_document/edit.html.twig', [
            'module_document' => $moduleDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="module_document_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ModuleDocument $moduleDocument): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$moduleDocument->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($moduleDocument);
            $entityManager->flush();
            $this->addFlash("delete", "Document deleted ");
        }

        return $this->redirectToRoute('module_document_index',['moduleId'=>$moduleDocument->getModule()->getId()]);
    }
    /**
     * @Route("/show/module/{id}/pdf", name="show_module_pdf", methods={"GET"})
     */
    public function showPdf(ModuleDocument $moduleDocument){
        return $this->render('module_document/showPdf.html.twig',["supportDocument"=>$moduleDocument->getSupportDocument(),'moduleId'=>$moduleDocument->getModule()->getId()]);
    }
}
