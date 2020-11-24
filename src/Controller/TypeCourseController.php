<?php

namespace App\Controller;

use App\Entity\TypeCourse;
use App\Form\TypeCourseType;
use App\Repository\TypeCourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/type/course")
 */
class TypeCourseController extends AbstractController
{
    /**
     * @Route("/", name="type_course_index", methods={"GET"})
     */
    public function index(TypeCourseRepository $typeCourseRepository): Response
    {
        return $this->render('type_course/index.html.twig', [
            'type_courses' => $typeCourseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="type_course_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $typeCourse = new TypeCourse();
        $form = $this->createForm(TypeCourseType::class, $typeCourse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($typeCourse);
            $entityManager->flush();
            $this->addFlash("success", "New type created ");

            return $this->redirectToRoute('type_course_index');
        }

        return $this->render('type_course/new.html.twig', [
            'type_course' => $typeCourse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_course_show", methods={"GET"})
     */
    public function show(TypeCourse $typeCourse): Response
    {
        return $this->render('type_course/show.html.twig', [
            'type_course' => $typeCourse,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_course_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TypeCourse $typeCourse): Response
    {
        $form = $this->createForm(TypeCourseType::class, $typeCourse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fieldsAvailable = [];
            foreach ($typeCourse->getFieldsAvailable() as $field) {
               if ($field != ""){
                   $fieldsAvailable[]=$field;
               }
            }
            $typeCourse->setFieldsAvailable($fieldsAvailable);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute('type_course_index');
        }

        return $this->render('type_course/edit.html.twig', [
            'type_course' => $typeCourse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_course_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TypeCourse $typeCourse): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$typeCourse->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typeCourse);
            $entityManager->flush();
            $this->addFlash("delete", "Course type deleted ");
        }

        return $this->redirectToRoute('type_course_index');
    }
}
