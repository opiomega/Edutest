<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Form\ClassesType;
use App\Repository\ClassesRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/classes")
 */
class ClassesController extends AbstractController
{
    /**
     * @Route("/", name="classes_index", methods={"GET","POST"})
     */
    public function index(CategoryRepository $categoryRepository,ClassesRepository $classesRepository,Request $request): Response
    {
        $class = new Classes();
        $form = $this->createForm(ClassesType::class, $class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($class);
            $entityManager->flush();
            $this->addFlash("success", "New class created ");
        }
        return $this->render('classes/index.html.twig', [
            'classes' => $classesRepository->findAll(),
            'form' => $form->createView(),
            'class'=>$class,
            'categories'=>$categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="classes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $class = new Classes();
        $form = $this->createForm(ClassesType::class, $class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($class);
            $entityManager->flush();
            $this->addFlash("success", "New classe created ");

            return $this->redirectToRoute('classes_index');
        }

        return $this->render('classes/new.html.twig', [
            'class' => $class,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="classes_show", methods={"GET"})
     */
    public function show(Classes $class): Response
    {
        return $this->render('classes/show.html.twig', [
            'class' => $class,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="classes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Classes $class): Response
    {
        $form = $this->createForm(ClassesType::class, $class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute('classes_index');
        }

        return $this->render('classes/edit.html.twig', [
            'class' => $class,
            'form' => $form->createView(),
        ]);
    }
      /**
     * @Route("/class/{id}", name="class_back_edit", methods={"GET","POST"})
     */
    public function classEdit(Request $request ,CategoryRepository $categoryRepository , Classes $class): Response
    {
            $className = $request->get('className');
            $classCategoryId = $request->get('classCategory');
            $class->setName($className);
            $category = $categoryRepository->find($classCategoryId);
            $class->setCategory($category);
            $this->getDoctrine()->getManager()->persist($class);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
            
            
            return $this->redirectToRoute('classes_index');
    }

    /**
     * @Route("/{id}", name="classes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Classes $class): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$class->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($class);
            $entityManager->flush();
            $this->addFlash("delete", "Classe deleted ");
        }

        return $this->redirectToRoute('classes_index');
    }
}
