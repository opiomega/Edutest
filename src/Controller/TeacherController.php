<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\EducationGroup;
use App\Form\UserType;
use App\Form\PhotoType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/teacher")
 */
class TeacherController extends Controller
{
    /**
     * @Route("/", name="teacher_index", methods={"GET"})
     */
    public function index(TeacherRepository $teacherRepository): Response
    {
        return $this->render('teacher/index.html.twig', [
            'teachers' => $teacherRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="teacher_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $new = [true];
        $userid=$request->query->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->find($userid);
        $teacher = new Teacher();
        $teacher->setUser($user) ; 
        $form = $this->createForm(TeacherType::class, $teacher,['new'=>$new]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('teacher/new.html.twig', [
            'teacher' => $teacher,
            'form' => $form->createView(),
        ]);
    }
    
 /**
     * @Route("/profile", name="profile_show", methods={"GET","POST"})
     */
    public function profile(Request $request,TeacherRepository $teacherRepository , UserRepository $userRepository , Security $security ): Response
    {
    $user    = $security->getUser() ; 
    $userid = $security->getUser()->getId();
    $date= $this->getUser()->getDatebirth();
    $date=\DateTime::createFromFormat('d/m/Y',$date);
    $curent = new \DateTime() ;
   
    $age= $curent->diff($date,true)->y;

    $teacher=$teacherRepository->findBy(['user' => $userid]) ;
    $teacherconverte = $teacher[0];
    $edit =[true];
    $form = $this->createForm(PhotoType::class, $user,["edit"=>$edit,'roles'=>$user->getRoles()]);
    $form->handleRequest($request);
   if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $user->setPhotoFile(null);
   }
        return $this->render('teacher/show.html.twig', [
            'teacher' =>  $teacherconverte ,
            'age'=>$age,
            'user' => $userRepository->find($userid),
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/{id}", name="teacher_show", methods={"GET","POST"})
     */
    public function show(Request $request,Teacher $teacher , UserRepository $userRepository,TeacherRepository $teacherRepository , Security $security): Response
    {
        $userid = $teacher->getUser()->getId() ;
        $user    = $security->getUser() ; 
        $date=$teacher->getUser()->getDatebirth();
        $date=\DateTime::createFromFormat('d/m/Y',$date);
        $curent = new \DateTime() ;
       
        $age= $curent->diff($date,true)->y;

        if ($security->getUser()->getStatus()=="Admin")
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($userid);
        else{
                $user = $security->getUser() ;
        }
       

        $teacher=$teacherRepository->findBy(['user' => $userid]) ;
    $teacherconverte = $teacher[0];
    $edit =[true];
    $form = $this->createForm(PhotoType::class, $user,["edit"=>$edit,'roles'=>$user->getRoles()]);
    $form->handleRequest($request);
   if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $user->setPhotoFile(null);
   }
        return $this->render('teacher/show.html.twig', [
            'teacher' =>  $teacherconverte ,
            'age'=>$age,
            'user' => $userRepository->find($userid),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="teacher_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Teacher $teacher): Response
    {
        $userStatus =  $this->getUser()->getStatus();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);
        $userRole = $this->getUser()?$this->getUser()->getRoles():[null];

        $teach=$this->getUser()->getTeacher(); 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            if($userStatus =="Admin"){
                $this->addFlash("update", "Informations updated ");
                return $this->redirectToRoute("teacher_show",["id"=>$teacher->getId()]);
            }
            elseif($userStatus =="Teacher"){
                $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute("profile_show",["id"=>$teach[0]->getId()]);
        }
        }

        return $this->render('teacher/edit.html.twig', [
            'teacher' => $teacher,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="teacher_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Teacher $teacher): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$teacher->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($teacher->getUser());
            $entityManager->flush();
            $this->addFlash("delete", "Teacher deleted ");
        }
        
        return $this->redirectToRoute('teacher_index');
    }
     /**
     * enable or disable user
     * @Route("/changeaction/{id}",name="teacher_change_status")
     * 
     * 
     */
    public function changeAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository("App:User")->find($id);
#var_dump($user);
        if($user!=null){
            if ($user->getActive()==1){
                $user->setActive(0);
                $this->addFlash("delete", "Teacher desactivated ");
               # die("1");
            }else{
                $user->setActive(1);
                $this->addFlash("success", "Teacher activated ");
                #die("2");
            }
            $em->persist($user);
            $em->flush();
        }
        return $this->redirectToRoute('teacher_index');
    }
    
    /**
     * @Route("/send/email/{id}", name="teacher_send_email", methods={"GET","POST"})
     */
    public function sendEmailToAllStudents(Request $request,EducationGroup $educationGroup,\Swift_Mailer $mailer){
         $defaultData = ['message' => ''];
        $form = $this->createFormBuilder($defaultData)
        ->add('object', TextType::class)
        ->add('message', TextareaType::class,['attr'=>['placeholder'=>'Type your message','class' => 'tinymce text-dark',
                    'data-theme' => 'bbcode' // Skip it if you want to use default theme
                    ]])
        ->add('send', SubmitType::class)
        ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $content = $data['message'];
            $object = $data['object'];
            //$content = $content=$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'counsoling'])?$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'counsoling'])->getContent(): "You have counsling ";
            $this->get('Emailing')->teacherEmail($educationGroup,$this->renderView(
                'email/teacherEmailSending.html.twig',
                ["content" => $content]),$mailer,$object,$this->getUser()->getEmail());
                $this->addFlash('success','Message sent');
        }
        return $this->render('email/teacherEmailWriting.html.twig',['form'=>$form->createView()]);
        
    }
}
