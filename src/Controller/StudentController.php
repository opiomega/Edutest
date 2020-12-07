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
use Curl\Curl;
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

      
        $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'http://5.189.159.53:8069/get_clients',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_POSTFIELDS =>'{
                    "params":{
                        
                    }
                }',
                  CURLOPT_HTTPHEADER => array(
                    'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                    'Content-Type: application/json',
                    'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
                  ),
                ));
                $entityManager = $this->getDoctrine()->getManager();
                $response = curl_exec($curl);
               // 
              //$reponse=json_encode($response);
              $data = json_decode($response, true);
                $res=$data['result']['response'];
              // die(json_encode($res));
              // die(json_encode($res));
                curl_close($curl);
               $students= $studentRepository->findAll();
               foreach($students as $s ){
                   foreach($res as $r){
                    $client_id=json_encode($r['student_id']);
                   // die($client_id);
                   $user=$s->getUser();
                       if($s->getId() == $r['student_id']){
                           $name=$r['name'];
                           $name = explode(" ", $name);
                           $first=$name[0];
                          
                           $last=$name[1];
                          // die($first);
                           $adress=$r['street'];
                           $email=$r['email'];
                           $phone=$r['phone'];
                           $zip=$r['zip'];
                           $city=$r['city'];
                           //$country=$r['country'];
                           $odoo=$r['partner_id'];

                            

                           $s->setFirstname($first);
                           $s->setLastname($last);
                           $s->setOdooId($odoo);
                           $user->setAdress($adress);
                           $user->setCity($city);
                           //$user->setCountry();
                           $user->setEmail($email);
                           $user->setPhone($phone);
                           $user->setZipcode($zip);

                           $entityManager->persist($s);
                           $entityManager->flush();
                           $entityManager->persist($user);
                           $entityManager->flush();
                          
                          
                       }
                   }
               }

	if ($request->get('error'))
            $this->addFlash('delete',$request->get('error'));

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Student')->createQueryBuilder('e');


        //list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
       # list($studentRepository, $pagerHtml) = $this->paginator($queryBuilder, $request);
        return $this->render('student/index.html.twig',  array(
            'students' => $studentRepository->findby([],['firstname'=>'ASC']),
            #'pagerHtml' => $pagerHtml,
            //'filterForm' => $filterForm->createView(),
        ));
    }
    
 
    
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
            //Get all clients
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://5.189.159.53:8069/get_clients',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_POSTFIELDS =>'{
                "params":{
                    
                }
            }',
              CURLOPT_HTTPHEADER => array(
                'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                'Content-Type: application/json',
                'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
              ),
            ));
            
            $response = curl_exec($curl);
           // 
          //$reponse=json_encode($response);
          $data = json_decode($response, true);
            $res=$data['result']['response'];
           
          // die(json_encode($res));
            curl_close($curl);
            //check by student id
            foreach($res as $r){
                if ($r['student_id']== $student->getId()){
                    die('student already exist in odoo');
                }
                else{
                    //create new client 
                    $curll = curl_init();

                curl_setopt_array($curll, array(
                CURLOPT_URL => 'http://5.189.159.53:8069/create_client',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "params":{
                
                "name":"'.$student->getFirstname().''." ".''.$student->getLastname().'",
                "satutpaiement":"Not Paid",
                "email":"'.$student->getUser()->getEmail().'",
                "phone":"'.$student->getUser()->getPhone().'",
                "occupation":"student",
                "street":"'.$student->getUser()->getAdress().'",
                "student_id":"'.$student->getId().'",
                "zip":"'.$student->getUser()->getZipcode().'",
                "city":"'.$student->getUser()->getCity().'",
                "country":"'.$student->getUser()->getCountry().'"
                
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
            'Content-Type: application/json',
            'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
        ),
        ));

            $responsenew = curl_exec($curll);


            $datanew = json_decode($responsenew, true);
           // var_dump($datanew);
          // die($responsenew);
         // $datanew=json_encode($datanew);
           $client_id=json_encode($datanew['result']['id']);
          //die($client_id);
          $client_id=(int)$client_id;
            curl_close($curll);

            //create new lead
            $curllead = curl_init();

                curl_setopt_array($curllead, array(
                CURLOPT_URL => 'http://5.189.159.53:8069/create_leads',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "params":{
                
                "name":"'.$student->getLevelTestType()->getName().'",
                "partner_id":'.$client_id.',
                "student_id":"'.$student->getId().'"
                
                
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
            'Content-Type: application/json',
            'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
        ),
        ));

            $responselead = curl_exec($curllead);


            $datanew = json_decode($responselead, true);
           // var_dump($datanew);
           die($responselead);
            curl_close($curllead);
            
                }
            }
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
                $firstname=$student->getFirstname();
        $lastname=$student->getLastname();
        $email=$student->getUser()->getEmail();
        $phone=$student->getUser()->getPhone();
        $adress=$student->getUser()->getAdress();
        $city=$student->getUser()->getCity();
        $zipcode=$student->getUser()->getZipcode();
        $country=$student->getUser()->getCountry();
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'http://5.189.159.53:8069/get_clients',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_POSTFIELDS =>'{
                    "params":{
                        
                    }
                }',
                  CURLOPT_HTTPHEADER => array(
                    'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                    'Content-Type: application/json',
                    'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
                  ),
                ));
                
                $response = curl_exec($curl);
               // 
              //$reponse=json_encode($response);
              $data = json_decode($response, true);
              //die(json_encode($data));
                $res=$data['result']['response'];
              
              
                curl_close($curl);
                //check by student id
                $i=0;
                foreach($res as $r){
                  //  var_dump($r['student_id']);
                    $client_id=(int)($r['student_id']);
                   // die($client_id);
                   //var_dump($user->getId());
                   //die(var_dump($r['id']));
                   
                    if($student->getId() == $r['student_id']){
                        //die('student already exist in odoo');
                       // die($r['id']);
                    

                        $id=$client_id;
                       
                        $curledit = curl_init();
    
                    curl_setopt_array($curledit, array(
                    CURLOPT_URL => 'http://5.189.159.53:8069/update_client',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                        "params":{
                            "id":'.$r['partner_id'].',
                    "name":"'.$student->getFirstname().''." ".''.$student->getLastname().'",
                    
                    "email":"'.$student->getUser()->getEmail().'",
                    "phone":"'.$student->getUser()->getPhone().'",
                   
                    "street":"'.$student->getUser()->getAdress().'",
                    "student_id":"'.$student->getId().'",
                    "zip":"'.$student->getUser()->getZipcode().'",
                    "city":"'.$student->getUser()->getCity().'",
                    "country_id":221
                    
                            }
                        }',
                        CURLOPT_HTTPHEADER => array(
                            'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                            'Content-Type: application/json',
                            'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
                        ),
                        ));
                        
                            $responsedit = curl_exec($curledit);
                            die(var_dump($responsedit));
                           // die(json_encode($responseedit));
                            $dataedit = json_decode($responsedit, true);
                        // var_dump($datanew);
                        // die($responsenew);
                        // $datanew=json_encode($datanew);
                       //$client_id=json_encode($dataedit['result']['id']);
                        //die($client_id);
                       // $client_id=(int)$client_id;
                            curl_close($curledit);

                    }
                   
                  /* else{
                        //create new client 
                       // die('2');
                        $curlnew = curl_init();
    
                    curl_setopt_array($curlnew, array(
                    CURLOPT_URL => 'http://5.189.159.53:8069/create_client',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                        "params":{
                    
                    "name":"'.$student->getFirstname().''." ".''.$student->getLastname().'",
                    
                    "email":"'.$student->getUser()->getEmail().'",
                    "phone":"'.$student->getUser()->getPhone().'",
                    
                    "street":"'.$student->getUser()->getAdress().'",
                    "student_id":"'.$student->getId().'",
                    "zip":"'.$student->getUser()->getZipcode().'",
                    "city":"'.$student->getUser()->getCity().'",
                    "country":"'.$student->getUser()->getCountry().'"
                    
                }
            }',
            CURLOPT_HTTPHEADER => array(
                'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                'Content-Type: application/json',
                'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
            ),
            ));
    
                $responsenew = curl_exec($curlnew);
    
    
               // $curlnew = json_decode($responsenew, true);
               // var_dump($datanew);
              // die($responsenew);
              $datanew=json_encode($curlnew);
              // $client_id=json_encode($dataedit['result']['id']);
              //die($client_id);
             // $client_id=(int)$client_id;
             
                curl_close($curlnew);
    

                
            }*/
            $i=$i+1;
            }
           
            $this->getDoctrine()->getManager()->flush();
            //die("$i");
            if($userStatus =="Admin" || $userStatus =="Student"){
                $this->addFlash("update", "Informations updated ");
                return $this->redirectToRoute("student_show",["id"=>$stud[0]->getId()]);
            }
         
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
    public function changeAction($id,\Swift_Mailer $mailer,StudentRepository $studentRepository)
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
       // die();
        $student=$studentRepository->findoneby(['user'=>$user]);

        /*
        //get odoo client by id

            $curll = curl_init();

            curl_setopt_array($curll, array(
            CURLOPT_URL => 'http://5.189.159.53:8069/get_client_id',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>'{
                "params":{
                    "id":"17"
                }
            }',
            CURLOPT_HTTPHEADER => array(
                'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                'Content-Type: application/json',
                'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
            ),
            ));

            $response = curl_exec($curll);
            $data = json_decode($response, true);
            var_dump($data);
            curl_close($curll);
            if($data != null ){
            $res=$data['result']['response'];
            $i=0;
        //Update lead status
            if ($res['id']== $student->getOdooId() or $res['email']==$user->getEmail()){
                $stat=$user->getStatus();
                if($status== 0 ){
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://5.189.159.53:8069/update_client',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                        "params":{
                            "id":'.$id.',
                            "status":"Not paid"
                            
                        }
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                        'Content-Type: application/json',
                        'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
                    ),
                    ));

                    $response = curl_exec($curl);


                    $data = json_decode($response, true);
                    var_dump($data);
                    curl_close($curl);
                }
                else{
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://5.189.159.53:8069/update_client',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                        "params":{
                            "id":'.$id.',
                            "status":"Paid"
                            
                        }
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                        'Content-Type: application/json',
                        'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
                    ),
                    ));

                    $response = curl_exec($curl);


                    $data = json_decode($response, true);
                    var_dump($data);
                    curl_close($curl);
                }
                
            }
            // if Student doesn't exist in odoo Create new odoo user
            else{

                $name=$student->getFirstname()." ".$student->getLastname();
                $email=$user->getEmail();
                $phone=$user->getPhone();
                $adress=$user->getAdress();
                $city=$user->getState();
                $zipcode=$user->getZipcode();
                $country=$user->getCountry();
                $id=$student->getId();
               
              
                
        
               //Create a new client(student) in odoo
                $curl = curl_init();
        
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://5.189.159.53:8069/create_client',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "params":{
                        "student_id:'.$id.'
                        "name":"'.$name.'",
                        "email":"'.$email.'",
                        "phone":"'.$phone.'",
                       
                        "street":"'.$adress.'",
                        
                        "zip":"'.$zipcode.'",
                        "city":"'.$city.'",
                        "country":"'.$country.'"
                        
                    }
                }',
                CURLOPT_HTTPHEADER => array(
                    'session_id: 4c61aba7cfed5814f565774e67077009a630c73b',
                    'Content-Type: application/json',
                    'Cookie: session_id=4c61aba7cfed5814f565774e67077009a630c73b'
                ),
                ));
        
                $response = curl_exec($curl);
                
                
                $data = json_decode($response, true);
                
                curl_close($curl);
            }
            
        }

       
die($response);*/
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

     /**
     * @Route("/profil/inbox", name="student_inbox", methods={"GET"})
     */
    public function inbox(Request $request,StudentRepository $studentRepository): Response
    {
        return $this->render('student/inbox.html.twig',  array(
           
        ));
    }
}
