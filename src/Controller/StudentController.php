<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Club;
use App\Entity\Application;
use App\Form\StudentType;
use App\Repository\TeacherRepository;
use App\Repository\ClubRepository;
use App\Repository\CandidatureRepository;
use App\Repository\StudentRepository;
use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Form\PhotoType;
//use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Candidature;
use App\Form\CandidatureType;
use App\Entity\Notification;
use App\Form\StudentFilterType;
use Doctrine\Common\Collections\Collection;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;

use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use  Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;
use App\Entity\Teacher;
use App\Entity\EducationGroup;
use App\Entity\TestScore;
use Symfony\Component\Form\FormError;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/", name="student_index", methods={"GET"})
     */
    public function index(Request $request,StudentRepository $studentRepository): Response
    {

      #  $searsh=new Student();
       # $filterForm=$this->createForm(StudentFilterType::class,$searsh);
        #$filterForm->handleRequest($request);
	if ($request->get('error'))
            $this->addFlash('delete',$request->get('error'));

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Student')->createQueryBuilder('e');


        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
       # list($studentRepository, $pagerHtml) = $this->paginator($queryBuilder, $request);
        return $this->render('student/index.html.twig',  array(
            'students' => $studentRepository->findby([],['firstname'=>'ASC']),
            #'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        ));
    }
    /*public function testFilterAction(Request $request)
    {
        $form = $this->get('form.factory')->create(ItemFilterType::class);

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // initialize a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('ProjectSuperBundle:MyEntity')
                ->createQueryBuilder('e');

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);

            // now look at the DQL =)
            var_dump($filterBuilder->getDql());
        }

        return $this->render('ProjectSuperBundle:Default:testFilter.html.twig', array(
            'form' => $form->createView(),
        ));
    }*/
 
    /**
    * Create filter form and process filter request.
    *
    */
  protected function filter($queryBuilder, Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('App\Form\StudentFilterType');
       
        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('StudentControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('StudentControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('StudentControllerFilter')) {
                $filterData = $session->get('StudentControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('App\Form\StudentFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

  
/*
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        $sortCol = $queryBuilder->getRootAlias().'.'.$request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show' , 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }
        
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me, $request)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('semester', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
        ));

        return array($entities, $pagerHtml);
    }
    
*/
      /**
     * @Route("/of/teacher/{id}", name="students_of_teacher_index", methods={"GET"})
     */
    public function indexTeacher(StudentRepository $studentRepository, TeacherRepository $teacherRepository,Request $request,EducationGroup $educationGroup): Response
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Student')->createQueryBuilder('e');
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);

        
      $teacher=$this->getUser()->getTeacher();
        /*$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb->select('s');
                $qb->from(Student::class, 's');
                $qb->join(Teacher::class,'t');
                $qb->join(EducationGroup::class,'e');
                $qb->where("s.educationGroups =e.id and t.id=e.teacher and t.id=:teacher");
                $qb->setParameter('teacher',$teacher[0]);
                $students = $qb->getQuery()->getResult();*/
                $students = $educationGroup->getStudents();

        $teacherCollection = $this->getUser()->getTeacher();
        if (isset($teacherCollection[0])){
            $teacher = $teacherRepository->find($teacherCollection[0]->getId());
      
            return $this->render('student/index.html.twig', [
                'students' => $students,
                'filterForm' => $filterForm->createView(),
            ]);
        }
        else {
            throw new Exception("This user is not a teacher");
        }
    }
      /**
     * @Route("/scores", name="students_socre_index", methods={"GET"})
     */
    public function indexScore(StudentRepository $studentRepository, TeacherRepository $teacherRepository,Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Student')->createQueryBuilder('e');
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        $teacher=$this->getUser()->getTeacher();
        if (isset($teacher[0])){
            $students = [];
            foreach ($teacher[0]->getEducationGroups() as $group){
                //var_dump($group->getName());
                $students = array_merge($group->getStudents()->getValues(),$students);
            }
            //var_dump($students);
           
      
            return $this->render('student/score.html.twig', [
                'students' => $students,
                'filterForm' => $filterForm->createView(),
            ]);
            /*$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb->select('s');
                $qb->from(Student::class, 's');
                $qb->join(Teacher::class,'t');
                $qb->join(EducationGroup::class,'e');
                $qb->join(TestScore::class,'test');
                $qb->where("s.educationGroups =e.id and t.id=e.teacher and t.id=:teacher and test.student=s.id");
                $qb->setParameter('teacher',$teacher[0]);
                $students = $qb->getQuery()->getResult();

        $teacherCollection = $this->getUser()->getTeacher();*/
        }
        else {
            $student=$studentRepository->findAll();
            return $this->render('student/score.html.twig', [
                'students' => $student,
                'filterForm' => $filterForm->createView(),
                ]);

        }
    }

    /**
     * @Route("/new", name="student_new", methods={"GET","POST"})
     */
     public function new(Request $request): Response
    {    
      
        $userid=$request->query->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->find($userid);
        $new = [true];
        $student = new Student();
        $student->setUser($user) ; 
        $user->setActive(0);
        //$student->setAccess(0); 
        $userRole = $this->getUser()?$this->getUser()->getRoles():[null];
        $form = $this->createForm(StudentType::class, $student, ['roles'=>$userRole,'new'=>$new]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date=new \DateTime();
            $app=$request->get('apply');
            if ($app== 'yes')
              $student->setTerm(1);
            else
              $student->setTerm(0);
            $entityManager = $this->getDoctrine()->getManager();
            $student->setCreatedat($date);
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/profil", name="profil_show", methods={"GET","POST"})
     */
    public function profile(ClubRepository $clubRepository,Request $request,StudentRepository $studentRepository , UserRepository $userRepository , Security $security ,ApplicationRepository $applicationRepository): Response
    {
        $user    = $security->getUser() ; 
        $userid = $security->getUser()->getId();

        $std=$this->getDoctrine()->getManager()->getRepository(Student::class)->findBy(["user"=>$userid]);
        $student= $this->getUser()->getStudent();
        $studentid=$student[0]->getId();
        $application=$applicationRepository->findBy(['student'=>$studentid]);
       $club=$student[0]->getClubsEngaged();
        $cand= $this->getDoctrine()->getManager()->getRepository(Candidature::class)->findBy(["student"=>$std]);
        $candidature=isset($cand[0])?$cand[0]:null;
        $date= $this->getUser()->getDatebirth();
        $date=\DateTime::createFromFormat('d/m/Y',$date);
        $curent = new \DateTime() ;
       
        $age= $curent->diff($date,true)->y;

        $edit = [true];
        $fieldDisabled = [false];
        if (isset($candidature)){
        if ($candidature->getIsSubmited())
            $fieldDisabled = [true];
        
        $form2 = $this->createForm(CandidatureType::class, $candidature, ["edit"=>$edit,"fieldDisabled"=>$fieldDisabled]);
        $form2->handleRequest($request);
        $verif = $candidature->getAllFieldsFull();
        if ($form2->isSubmitted() && $form2->isValid()) {
            //if ($reques)
            if ($request->get('submitCandiature')){
                $today = new \DateTime() ;
                $candidature->setIsSubmited(true);
                $notification = new Notification();
                
                $notification->setTransmitter($candidature->getStudent());
                $notification->setContent('new <a href="{{ path(\'candidature_show\',{\'id\':\''.$candidature->getId().'\'}}) }}">candidature</a> from '.$candidature->getStudent()->getFirstName().' '.$candidature->getStudent()->getLastName());
                $notification->setCandidature($candidature);
                $notification->setDate($today);
                $notification->setSeen(false);
                $this->getDoctrine()->getManager()->persist($notification);
                $fieldDisabled = [true];
                $form2 = null;
                $form2 = $this->createForm(CandidatureType::class, $candidature, ["edit"=>$edit,"fieldDisabled"=>$fieldDisabled]);
                $form2->handleRequest($request);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
        }
    }
    else {
        $candidature = new Candidature();
        $form2 = $this->createForm(CandidatureType::class, $candidature, ["edit"=>$edit,"fieldDisabled"=>$fieldDisabled]);
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $student = $this->getUser()->getStudent();
            if (isset($student[0]))
                $candidature->setStudent($student[0]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();
            $this->addFlash("success", "New candidature created ");

        }
    }
        $student=$studentRepository->findBy(['user' => $userid]) ;
        $studentconverte = $student[0];
        $edit = [true];
        $form = $this->createForm(PhotoType::class, $user,["edit"=>$edit,'roles'=>$user->getRoles()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $user->setPhotoFile(null);
        }

       ;
        $candidatures= $this->getDoctrine()->getManager()->getRepository(Candidature::class)->findBy(["student"=>$student]);

        
            return $this->render('student/show.html.twig', [
                'student' =>  $studentconverte ,
                'age'=>$age,
                'candidatures' => $candidatures,
                'user' => $userRepository->find($userid),
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'clubs'=> $club,
                'applications'=>$application
            ]);
    }
    /**
     * @Route("show/{id}", name="student_show", methods={"GET","POST"})
     */
    public function show(Request $request,Student $student , UserRepository $userRepository,StudentRepository $studentRepository , Security $security,ApplicationRepository $applicationRepository): Response
    { 
        $userId = $student->getUser()->getId();
        $date= $student->getUser()->getDatebirth();
        $date=\DateTime::createFromFormat('d/m/Y',$date);
        $curent = new \DateTime() ;
        $std=$student->getId();
       
        $studentid=$student->getId();
        $application=$applicationRepository->findBy(['student'=>$studentid]);
       $club=$student->getClubsEngaged();
        $candidatures= $this->getDoctrine()->getManager()->getRepository(Candidature::class)->findBy(["student"=>$std]);
        $age= $curent->diff($date,true)->y;
if ($security->getUser()->getStatus()=="Admin")
$user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($userId);
else{
        $user = $security->getUser() ;

    }
        $userid = $student->getUser()->getId() ;
        $student=$studentRepository->findBy(['user' => $userid]) ;
        $studentconverte = $student[0];
        $edit = [true];
        $form = $this->createForm(PhotoType::class, $user,["edit"=>$edit,'roles'=>$user->getRoles()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $user->setPhotoFile(null);
                
        }

        return $this->render('student/show.html.twig', [
                'student' =>  $studentconverte ,
                'age'=>$age,
                'user' => $userRepository->find($userid),
                'form' => $form->createView(),
                'candidatures' => $candidatures,
                'clubs'=> $club,
                'applications'=>$application
            
        ]);
    }

   

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Student $student, UserRepository $userRepository): Response
    {
        $userId = $student->getUser()->getId();
        $userStatus =  $this->getUser()->getStatus();
         $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($userId);
         $userRole = $this->getUser()?$this->getUser()->getRoles():[null];
        $form = $this->createForm(StudentType::class, $student, ['roles'=>$userRole]);
        $form->handleRequest($request);
        if( $userStatus =="Student")
        $stud=$this->getUser()->getStudent();
        else
        $stud[0]=$student;
      
        
        if ($form->isSubmitted() && $form->isValid()) {
            $userEmail = $userRepository->findBy(["email" => $form->get('email')->getData()]);
            if (isset($userEmail[0]) && $userEmail[0]!=$student->getUser()) {
                $form->get('email')->addError(new FormError('email exist !!!'));
                return $this->render('student/edit.html.twig', [
                    'student' => $student,
                    'form' => $form->createView(),
                ]);
            }else {
                $this->getDoctrine()->getManager()->flush();
            }
            if($userStatus =="Admin" || $userStatus =="Student"){
                $this->addFlash("update", "Informations updated ");
                return $this->redirectToRoute("student_show",["id"=>$stud[0]->getId()]);
            }
         }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
  
}

    /**
     * @Route("delete/{id}", name="student_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Student $student): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
            $this->addFlash("delete", "Student  deleted ");
        }
        

        return $this->redirectToRoute('student_index');
    }

     /**
     * enable or disable user
     * @Route("/changeaction/{id}",name="change_status")
     * 
     * 
     */
    public function changeAction($id,\Swift_Mailer $mailer)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository("App:User")->find($id);
#var_dump($user);
        if($user!=null){
            if ($user->getActive()==1){
                $user->setActive(0);
                $this->addFlash("delete", "Student desactivated ");

                $email = $user->getEmail();
                $message = (new \Swift_Message('Account disabled'))
                ->setFrom('sami.maazaoui.adictest@gmail.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        // templates/emails/registration.html.twig
                        'user/emaildisabled.html.twig',
                         ["user" => $user]
                    ),
                'text/html'
                );
                
                $mailer->send($message);
               # die("1");
            }else{
                $user->setActive(1);
                $this->addFlash("success", "Student activated ");
                #die("2");


                $email = $user->getEmail();
                $message = (new \Swift_Message('Account Enabled'))
                ->setFrom('sami.maazaoui.adictest@gmail.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        // templates/emails/registration.html.twig
                        'user/emailenabled.html.twig',
                         ["user" => $user]
                    ),
                'text/html'
                );
                
                $mailer->send($message);
            }
            $em->persist($user);
            $em->flush();
        }
        return $this->redirectToRoute('student_index');
    }



 /**
     * enable or disable user
     * @Route("/payaction/{id}",name="pay")
     * 
     * 
     */
    public function payAction($id)
    {
        $time = new \DateTime();
        $date= $time;

        

        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository("App:Student")->find($id);
#var_dump($user);
        if($user!=null){
            $user->setPaymentday($time);
            $em->persist($user);
            $em->flush();
            $date->modify('+30 days');
            $user->setNextpaymentday($date);
            $em->flush();
        }
        return $this->redirectToRoute('student_index');
    }
    /**
     * @Route("/payement/{id}/edit", name="payement_edit", methods={"GET","POST"})
     */
    public function editpayement(Request $request, $id): Response
    {
        $em=$this->getDoctrine()->getManager();
        $student=$em->getRepository("App:Student")->find($id);
        if($student!=null){
        $payement=$student->getPaymentday();
        $next=$student->getNextpaymentday();
        
        $payementd=$request->get('payday');
        $payementd= \DateTime::createFromFormat('m/d/Y',$payementd);
       
        
        $nextd=$request->get('nextday');
      
        $nextd= \DateTime::createFromFormat('m/d/Y',$nextd);
       
        if ($request->isMethod('POST')) {
        $student->setPaymentday($payementd);
        $em->persist($student);
        $em->flush();
      
        
        $student->setNextpaymentday($nextd);
        $em->flush();
         
            $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute('student_index');
        }
        }

        return $this->render('student/payement.html.twig', [
            'student' => $student,
           
            
        ]);
    }

 /**
     * enable or disable user
     * @Route("/accessaction/{id}",name="access_status")
     * 
     * 
     */
     public function accessAction($id,\Swift_Mailer $mailer)
     {
         $em=$this->getDoctrine()->getManager();
         $student=$em->getRepository("App:Student")->find($id);
 #var_dump($user);
         if($student!=null){
             if ($student->getAccess()==1){
                 $student->setAccess(0);
                 $this->addFlash("delete", "Student desactivated ");
 
                 $email = $student->getUser()->getEmail();
                 $message = (new \Swift_Message('Account disabled'))
                 ->setFrom('sami.maazaoui.adictest@gmail.com')
                 ->setTo($email)
                 ->setBody(
                     $this->renderView(
                         // templates/emails/registration.html.twig
                         'user/emaildisabled.html.twig',
                          ["user" => $student]
                     ),
                 'text/html'
                 );
                 
                 $mailer->send($message);
                # die("1");
             }else{
                 $student->setAccess(1);
                 //$student->setLevelTest(1);
                 $this->addFlash("success", "Student activated ");
                 #die("2");
 
 
                 $email = $student->getUser()->getEmail();
                 $message = (new \Swift_Message('Account Enabled'))
                 ->setFrom('sami.maazaoui.adictest@gmail.com')
                 ->setTo($email)
                 ->setBody(
                     $this->renderView(
                         // templates/emails/registration.html.twig
                         'user/emailenabled.html.twig',
                          ["user" => $student]
                     ),
                 'text/html'
                 );
                 
                 $mailer->send($message);
             }
             $em->persist($student);
             $em->flush();
         }
         return $this->redirectToRoute('student_index');

        }

    /**
     * @Route("/application", name="application_new", methods={"GET","POST"})
     */
    public function newapplication(Request $request,ApplicationRepository $applicationRepository): Response
    {
        $application = new Application();
        $student=$this->getUser()->getStudent();
        $studentid=$student[0]->getId();
        $applicationn=$applicationRepository->findBy(['student'=>$studentid]);
        $university=$request->get('univer');
        $count=count($applicationn);
         
        if ($count==20){
            $this->addFlash("delete", "You have reached the maximum number of unviversities allowed to apply");
            return $this->redirectToRoute('univ_student_index');
        }

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                        $qb->select('a');
                        $qb->from(Application::class, 'a');
                        $qb->join(Student::class,'s');
                        $qb->where("a.universityname = :university and a.student= :student");
                        $qb->setParameter('university',$university);
                        $qb->setParameter('student',$studentid);
                        $result = $qb->getQuery()->getResult();
        if ($result){
            $this->addFlash("delete", "Aleardy applied to ".$university."!!");
            return $this->redirectToRoute('univ_student_index');
        }
        $email=$request->get('email');
        $password=$request->get('pass');
        $student=$this->getUser()->getStudent();
        $studentid=$student[0];
        $application->setEmail($email);
        $application->setPassword($password);
        $application->setStudent($studentid);
        $application->setUniversityname($university);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($application);
        $entityManager->flush();
        $this->addFlash("success", "Applied successfuly!! ");
        return $this->redirectToRoute('univ_student_index');

      
            
       
    }
         /**
     * @Route("/application/index", name="application_index", methods={"GET","POST"})
     */
    public function indexapplication(Request $request,ApplicationRepository $applicationRepository,CandidatureRepository $candidatureRepository): Response
    {
        $student=$this->getUser()->getStudent();
        $studentid=$student[0]->getId();

       
        $candidature=$candidatureRepository->findBy(['student'=>$studentid]);
        $application=$applicationRepository->findBy(['student'=>$studentid]);

        return $this->render('student/application.html.twig',  array(
            'application' => $application,
            'candidature'=>$candidature
           
        ));
    }

    /**
     * @Route("/application/edit/{id}", name="application_edit", methods={"GET","POST"})
     */
    public function modif(Request $request, Application $application)
    {
        $name=$request->get('email');
        $description=$request->get('password');
        $application->setEmail($name);
        $application->setPassword($description);
        $this->getDoctrine()->getManager()->persist($application);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("update", "Application updated ");
        return $this->redirectToRoute('application_index');

       
    }

    /**
     * @Route("/application/{id}", name="application_delete", methods={"DELETE"})
     */
    public function applicationdelete(Request $request, Application $application): Response
    {
        if ($this->isCsrfTokenValid('delete'.$application->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($application);
            $entityManager->flush();
            $this->addFlash("delete", "Application deleted ");
        }

        return $this->redirectToRoute('application_index');
    }
}
