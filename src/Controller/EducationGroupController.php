<?php

namespace App\Controller;

use App\Entity\EducationGroup;
use App\Form\EducationGroupType;
use App\Repository\EducationGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/education/group")
 */
class EducationGroupController extends AbstractController
{
    /**
     * @Route("/", name="education_group_index", methods={"GET"})
     */
    public function index(EducationGroupRepository $educationGroupRepository): Response
    {
        return $this->render('education_group/index.html.twig', [
            'education_groups' => $educationGroupRepository->findBy(['online'=>false]),
        ]);
    }
    
    
    /**
     * @Route("/online", name="education_group_online_index", methods={"GET"})
     */
    public function indexOnline(EducationGroupRepository $educationGroupRepository): Response
    {
        return $this->render('education_group/indexOnline.html.twig', [
            'education_groups' => $educationGroupRepository->findBy(['online'=>true]),
        ]);
    }
    
    /**
     * @Route("/teacher", name="education_group_teacher_index", methods={"GET"})
     */
    public function indexTeacher(EducationGroupRepository $educationGroupRepository): Response
    {
        $teacher = $this->getUser()->getTeacher();
        if ($this->getUser()->getStatus()!="Teacher"){
            $this->addFlash('danger','This user is not a teacher');
            return $this->redirectToRoute('default');
        }
        return $this->render('education_group/indexTeacher.html.twig', [
            'education_groups' => $teacher[0]->getEducationGroups(),
        ]);
    }


    /**
     * @Route("/new", name="education_group_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $educationGroup = new EducationGroup();
        $form = $this->createForm(EducationGroupType::class, $educationGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*$students = $form->get('students')->getData();
            $educationGroup->setStudents($students);*/
            $entityManager = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $educationGroup->setOnline(false);
            $errorStudent = false;
            $errorStudentMessage = 'You can\'t add these students (';
            foreach ($data->getStudents() as $student){
                if ($student->getOnline()){
                    $errorStudent = true;
                    $errorStudentMessage .= $student->getFirstname().' '.$student->getLastname();
                    $data->removeStudent($student);
                    $educationGroup->removeStudent($student);
                }
            }
            if ($errorStudent){
                $onOffLine = 'Offline';
                if ($educationGroup->getOnline())
                    $onOffLine = 'Online';
                $errorStudentMessage .=') they don\'t want to study '.$onOffLine.'.';
                $this->addFlash('danger',$errorStudentMessage);
                
                /*return $this->render('education_group/new.html.twig', [
                                        'education_group' => $educationGroup,
                                        'form' => $form->createView(),
                    ]);*/
            }
            
            $entityManager->persist($educationGroup);
            $entityManager->flush();

            return $this->redirectToRoute('education_group_index');
        }

        return $this->render('education_group/new.html.twig', [
            'education_group' => $educationGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/online", name="education_group_online_new", methods={"GET","POST"})
     */
    public function newOnline(Request $request): Response
    {
        $educationGroup = new EducationGroup();
        $online = [true];
        $form = $this->createForm(EducationGroupType::class, $educationGroup,['online'=>$online]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $educationGroup->setOnline(true);
            $errorStudent = false;
            $errorStudentMessage = 'You can\'t add these students (';
            foreach ($data->getStudents() as $student){
                if (!$student->getOnline()){
                    $errorStudent = true;
                    $errorStudentMessage .= $student->getFirstname().' '.$student->getLastname();
                    $data->removeStudent($student);
                    $educationGroup->removeStudent($student);
                }
            }
            if ($errorStudent){
                $errorStudentMessage .=') they don\'t want to study online.';
                $this->addFlash('danger',$errorStudentMessage);
            }
            //$educationGroup->setTeacher(null);
            
            $entityManager->persist($educationGroup);
            $entityManager->flush();

            return $this->redirectToRoute('education_group_online_index');
        }

        return $this->render('education_group/new.html.twig', [
            'education_group' => $educationGroup,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/{id}", name="education_group_show", methods={"GET"})
     */
    public function show(EducationGroup $educationGroup): Response
    {
        return $this->render('education_group/show.html.twig', [
            'education_group' => $educationGroup,
        ]);
    }
    
    /**
     * @Route("/{id}/online", name="education_group_online_show", methods={"GET"})
     */
    public function showOnline(EducationGroup $educationGroup): Response
    {
        return $this->render('education_group/showOnline.html.twig', [
            'education_group' => $educationGroup,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="education_group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EducationGroup $educationGroup): Response
    {
        $form = $this->createForm(EducationGroupType::class, $educationGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $students = $form->getData();
            /*var_dump($request->get('education_group[students]'));
            die();
            $educationGroup->setStudents($students);*/
            $data = $form->getData();
            $errorStudent = false;
            $errorStudentMessage = 'You can\'t add these students (';
            foreach ($data->getStudents() as $student){
                if ($student->getOnline()){
                    $errorStudent = true;
                    $errorStudentMessage .= $student->getFirstname().' '.$student->getLastname();
                    $data->removeStudent($student);
                    $educationGroup->removeStudent($student);
                }
            }
            if ($errorStudent){
                $errorStudentMessage .=') they don\'t want to study offline.';
                $this->addFlash('danger',$errorStudentMessage);
                
                /*return $this->render('education_group/new.html.twig', [
                                        'education_group' => $educationGroup,
                                        'form' => $form->createView(),
                    ]);*/
            }
            $this->getDoctrine()->getManager()->persist($educationGroup);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('education_group_index');
        }

        return $this->render('education_group/edit.html.twig', [
            'education_group' => $educationGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit/online", name="education_group_online_edit", methods={"GET","POST"})
     */
    public function editOnline(Request $request, EducationGroup $educationGroup): Response
    {
        $online = [true];
        $form = $this->createForm(EducationGroupType::class, $educationGroup,['online'=>$online]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $students = $form->getData();
            /*var_dump($request->get('education_group[students]'));
            die();
            $educationGroup->setStudents($students);*/
            $data = $form->getData();
            $errorStudent = false;
            $errorStudentMessage = 'You can\'t add these students (';
            foreach ($data->getStudents() as $student){
                if (!$student->getOnline()){
                    $errorStudent = true;
                    $errorStudentMessage .= $student->getFirstname().' '.$student->getLastname();
                    $data->removeStudent($student);
                    $educationGroup->removeStudent($student);
                }
            }
            if ($errorStudent){
                $errorStudentMessage .=') they don\'t want to study online.';
                $this->addFlash('danger',$errorStudentMessage);
                
                /*return $this->render('education_group/new.html.twig', [
                                        'education_group' => $educationGroup,
                                        'form' => $form->createView(),
                    ]);*/
            }
            //$educationGroup->setTeacher(null);
            $this->getDoctrine()->getManager()->persist($educationGroup);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('education_group_online_index');
        }

        return $this->render('education_group/edit.html.twig', [
            'education_group' => $educationGroup,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/{id}", name="education_group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EducationGroup $educationGroup): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$educationGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($educationGroup);
            $entityManager->flush();
        }
        if ($educationGroup->getOnline())
            return $this->redirectToRoute('education_group_online_index');
        return $this->redirectToRoute('education_group_index');
    }
    
    /**
     * @Route("/index/teacher/ct/{id}/{list}", name="module_test_index_teacher", methods={"GET"})
     */
    public function indexTeacherCoursesTest(EducationGroup $educationGroup,$list="cousres"): Response
    {
        if ($list=="tests")
            return $this->render('test/indexTeacher.html.twig', [
                'educationGroup'=>$educationGroup
            ]);
        return $this->render('module/indexTeacher.html.twig', [
            'educationGroup'=>$educationGroup
        ]);
    }
}
