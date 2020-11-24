<?php

namespace App\Controller;

use App\Entity\Counsling;
use App\Entity\Days;
use App\Entity\StudentConsling;
use App\Entity\Teacher;
use App\Entity\Student;
use App\Form\Counsling1Type;
use App\Form\CounslingType;
use App\Repository\CounslingRepository;
use App\Repository\DaysRepository;
use App\Repository\EducationGroupRepository;
use App\Repository\StudentRepository;
use App\Repository\StudentConslingRepository;
use App\Repository\TeacherRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/counsling")
 */
class CounslingController extends Controller
{
    /**
     * @Route("/", name="counsling_index", methods={"GET"})
     */
    public function index(CounslingRepository $counslingRepository): Response
    {
         $teacher=$this->getUser()->getTeacher();
$couns=$counslingRepository->findBy(['teacher'=>$teacher[0]]);
        return $this->render('counsling/index.html.twig', [
            'counslings' => $couns,
        ]);
    }

    /**
     * @Route("/new", name="counsling_new", methods={"GET","POST"})
     */
    public function new(Request $request,EducationGroupRepository $educationGroupRepository,\Swift_Mailer $mailer): Response
    {
        $counsling = new Counsling();
        $form = $this->createForm(CounslingType::class, $counsling);
        $form->handleRequest($request);
        $date=$request->get('date');
        $teacher=$this->getUser()->getTeacher();
        $d=new \DateTime($date);
        
        $group=$educationGroupRepository->findby(['teacher'=>$teacher[0]->getId()]);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $gp=$request->get('g');
            $gp=$educationGroupRepository->findOneBy(['id'=>$gp]);
            $entityManager = $this->getDoctrine()->getManager();
            $counsling->setTeacher($teacher[0]);
            $counsling->setDate($d);
            $counsling->setEducationgroup($gp);
            $entityManager->persist($counsling);
            $entityManager->flush();
	    $content = $content=$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'counseling'])?$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'counseling'])->getContent(): "You have counsling ";
             $this->get('Emailing')->counslingEmail($counsling->getEducationgroup(),$this->renderView(
                        'email/emailCounsoling.html.twig',
                        ["content" => $content,"date"=>$d->format("d-m-Y"),"startTime"=>$counsling->getBegintime(),"endTime"=>$counsling->getEndtime()]),$mailer);

            return $this->redirectToRoute('counsling_index');
        }

        return $this->render('counsling/new.html.twig', [
            'counsling' => $counsling,
            'form' => $form->createView(),
            'group'=>$group
        ]);
    }

    /**
     * @Route("/{id}", name="counsling_show", methods={"GET"})
     */
    public function show(Counsling $counsling): Response
    {
        return $this->render('counsling/show.html.twig', [
            'counsling' => $counsling,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="counsling_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Counsling $counsling): Response
    {
        $form = $this->createForm(CounslingType::class, $counsling);
        $form->handleRequest($request);
        $date=$request->get('date');
        $d=new \DateTime($date);
        if ($form->isSubmitted() && $form->isValid()) {
            $counsling->setDate($d);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('counsling_index');
        }

        return $this->render('counsling/edit.html.twig', [
            'counsling' => $counsling,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="counsling_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Counsling $counsling): Response
    {
        /*if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");*/
        if ($this->isCsrfTokenValid('delete'.$counsling->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($counsling);
            $entityManager->flush();
        }

        return $this->redirectToRoute('counsling_index');
    }

    /**
     * @Route("/teacher/sessions", name="enseignant_session")
     */
    public function listsessionteacherAction(EducationGroupRepository $educationGroupRepository,CounslingRepository $counslingRepository,StudentConslingRepository $studentConslingRepository): Response
    {
        $em=$this->getDoctrine()->getManager();
        $teacher=$this->getUser()->getTeacher();
        
        $classe=$educationGroupRepository->findBy(['teacher'=>$teacher[0]->getId()]);
       $counslin=$counslingRepository->findBy(['teacher'=>$teacher[0]->getId()]);
      $se = $counslingRepository->findBy(["teacher"=>$teacher[0]->getId()]);
      $c=count($se);
    $count=count($counslin);
    $finir=[];
    $ids=[];
    $name=[];
    $startTime=[];
            $endtime=[];
    for($m=0;$m<$count;$m++){
        $nam=$se[$m]->getEducationgroup()->getName();
          $name=$nam;
          $date=$counslin[$m]->getDate()->format('Y-m-d');
          $ids[]=$counslin[$m]->getId();
        //  dump($date);
          $begin=$counslin[$m]->getBegintime();
        $end=$counslin[$m]->getEndtime();
       

        $startTime[]=date('Y-m-d H:i',strtotime("$date $begin"));
        
        $endtime[]=date('Y-m-d H:i',strtotime("$date $end"));
      
        
    }
  
      

        return $this->render('counsling/sessionlist.html.twig', array(
            //'seances' => $seance,
            'classe'=>$classe,
            'starttime'=>$startTime,
            'endtime'=>$endtime,
           // 'debut'=>$debut,
            'finir'=>$finir,
            'name'=>$name,
            'id'=>$ids
        ));
    }

    /**
     * @Route("/student/book/{id}", name="counsling_student", methods={"GET","POST"})
     */
    public function book($id,Request $request,CounslingRepository $counslingRepository,StudentConslingRepository $studentConslingRepository): Response
    {
        $scounsling = new StudentConsling();
        $couns=$counslingRepository->findOneBy(["id"=>$id]);
        $date=$couns->getDate();
        $teacher=$couns->getTeacher();
        $begin=$couns->getBegintime();
        $end=$couns->getEndtime();
       
        $studentcons=$studentConslingRepository->findby(['counsling'=>$couns]);
        $studentconsend=[];
        $studentconsbegin=[];
     
        foreach($studentcons as $s){
        $studentconsend[]=$s->getSend();
        $studentconsbegin[]=$s->getSbegin();

        }
        $diff=(int)$end-(int)$begin;
       $timebegin= strtotime($begin);
       $timeend= strtotime($end);
     
        $j=0;
        $startTime=[];
        $endtime=[];
        #$startTime[0]=date("H:i",strtotime('00:00'));
        for($i=0;$i<=$diff*2-1;$i++){
            $startTime[] = date("H:i", strtotime('+'.$j.' minutes', $timebegin));
          
           # $endtime[]=date("H:i", strtotime('-'.$j.'minutes', $timeend));
               $j=$j+30;

           # dump($startTime);
            #dump($endtime);
        }
        $k=0;
        for($i=0;$i<=$diff*2;$i++){
            $endtime[]=date("H:i", strtotime('-'.$k.'minutes', $timeend));
            $k=$k+30;
        }
        $student=$this->getUser()->getStudent();
$selectdate=$request->get('start');
$selectend=$request->get('end');
$topic=$request->get('topic');
$comment=$request->get('comments');
$medium=$request->get('mediumm');
$teacher=$couns->getTeacher();
 // Filter action
 if ($request->get('new') == 'add') {
    // Bind values from the request
    
$scounsling->setComments($comment);
$scounsling->setMedium($medium);
$scounsling->setStudent($student[0]);
$scounsling->setSbegin($selectdate);
$scounsling->setSend($selectend);
$scounsling->setTopic($topic);
$scounsling->setDate($date);
$scounsling->setTeacher($teacher);
$scounsling->setCounsling($couns);

$entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($scounsling);
            $entityManager->flush();
            return $this->redirectToRoute('student_sessions_index');
 }
        #$counsling->setTeacher($teacher[0]);
        #$counsling->setDate($date);
       
        return $this->render('counsling/book.html.twig', [
            'scounsling' => $scounsling,
            'counsling'=>$couns,
            'diff'=>$diff,
            'begin'=>$startTime,
            'end'=>$endtime,
            'days'=>$date,
            'studentconsbegin'=>$studentconsbegin,
            'studentconsend'=>$studentconsend
            
        ]);
    }

    /**
     * @Route("/student/sessions",name="student_sessions_index")
     */
    public function sessionsindex(CounslingRepository $counslingRepository,StudentConslingRepository $studentConslingRepository){
        $student=$this->getUser()->getStudent();
        
        $educationgroups=$student[0]->getEducationGroups();
        $organizedArray=array();
        //for($i=1;$i<8;$i++){
        $se = [];
        foreach ($educationgroups as $educationGroup){
            $se = array_merge($se,$educationGroup->getCounslings()->getValues());
        }

        //dump($se);
        //$se=$counslingRepository->findBy(["educationgroup"=>$educationgroup->getId()]);
        //array_push($organizedArray,$se);
foreach($se as $c){
        $begin=$c->getBegintime();
        $end=$c->getEndtime();
        

        $diff=(int)$end-(int)$begin;
        
}
        //}
        $c=count($se);
        // dump($c);
         $debut=[];
         $finir=[];
         $ids=[];
         $name=[];
	 $startTime=[];
         $endtime=[];
         for($j=0;$j<$c;$j++){
             $id=$se[$j]->getId();
             $ids[]=$id;
             //dump($ids);
             $nam=$se[$j]->getEducationgroup()->getName();
             $name=$nam;
             $booked=$studentConslingRepository->findBy(['counsling'=>$id]);
             $co=count($booked);
           $begin=$se[$j]->getBegintime();
           $end=$se[$j]->getEndtime();
           $timebegin= strtotime($begin);
           $timeend= strtotime($end);
        // dump($co);
           $date=$se[$j]->getDate()->format('Y-m-d');
        
                   $startTime[]=date('Y-m-d H:i',strtotime("$date $begin"));
              
                   $endtime[]=date('Y-m-d H:i',strtotime("$date $end"));
                  

               }
           
        //dump($startTime); 

        return $this->render('counsling/sessionlist.html.twig', array(
            'seances' => $organizedArray,
            //'classe'=>$educationgroup,
            //'diff'=>$diff,
            'starttime'=>$startTime,
            'endtime'=>$endtime,
            'debut'=>$debut,
            'finir'=>$finir,
            'name'=>$name,
            'id'=>$ids
            
        ));
    }
     /**
     * @Route("/booked/{id}" ,name="booked_student_index")
     */
    public function studentindex($id,StudentConslingRepository $studentConslingRepository,TeacherRepository $teacherRepository){
       $teacher=$teacherRepository->findOneBy(['id'=>$id]);
        $students=$studentConslingRepository->findBy(['teacher'=>$teacher]);

        $date=new \DateTime();
        $date=$date->format('Y-m-d');
     $time=new \DateTime();
     $time= $time->format('H:i');
     //dump($time);

$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
$qb->select('sc');
$qb->from(StudentConsling::class, 'sc');
$qb->join(Teacher::class,'t');
$qb->where("sc.date >:date and sc.teacher=:teacher ");
$qb->setParameter('date',$date);
#$qb->setParameter('time',$time);
$qb->setParameter('teacher',$teacher);
$nextcounslings = $qb->getQuery()->getResult();


$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
$qb->select('sc');
$qb->from(StudentConsling::class, 'sc');
$qb->join(Teacher::class,'t');
$qb->where("sc.date <:date and t.id=:teacher ");
$qb->setParameter('date',$date);
#$qb->setParameter('time',$time);
$qb->setParameter('teacher',$teacher);
$prevcounslings = $qb->getQuery()->getResult();

$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
$qb->select('sc');
$qb->from(StudentConsling::class, 'sc');
$qb->join(Teacher::class,'t');
$qb->where("sc.date =:date and t.id=:teacher ");
$qb->setParameter('date',$date);
#$qb->setParameter('time',$time);
$qb->setParameter('teacher',$teacher);
$today = $qb->getQuery()->getResult();


        return $this->render('counsling/studentbooked.html.twig', array(
            'students'=>$students,
            'next'=>$nextcounslings,
            'prev'=>$prevcounslings,
            'today'=>$today
        ));
    }

    /**
     * @Route("/booked/from/student/{id}" ,name="booked_student_from_student_index")
     */
    public function studentFromStudentindex($id,StudentConslingRepository $studentConslingRepository, StudentRepository $studentRepository){
        
        $student=$studentRepository->findOneBy(['id'=>$id]);
        $teachers=$studentConslingRepository->findBy(['student'=>$student]);
        /*$teachers=[];
        foreach ($student->getEducationGroups() as $educationGroup){
            if (!in_array($educationGroup->getTeacher(), $teachers))
                    $teachers[]=$educationGroup->getTeacher();
        }*/
            

        $date=new \DateTime();
        $date=$date->format('Y-m-d');
     $time=new \DateTime();
     $time= $time->format('H:i');
     //dump($time);

$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
$qb->select('sc');
$qb->from(StudentConsling::class, 'sc');
$qb->join(Teacher::class,'t');
$qb->where("sc.date >:date and sc.student=:student ");
$qb->setParameter('date',$date);
#$qb->setParameter('time',$time);
$qb->setParameter('student',$student);
$nextcounslings = $qb->getQuery()->getResult();


$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
$qb->select('sc');
$qb->from(StudentConsling::class, 'sc');
$qb->join(Student::class,'s');
$qb->where("sc.date <:date and s.id=:student ");
$qb->setParameter('date',$date);
#$qb->setParameter('time',$time);
$qb->setParameter('student',$student);
$prevcounslings = $qb->getQuery()->getResult();

$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
$qb->select('sc');
$qb->from(StudentConsling::class, 'sc');
$qb->join(Student::class,'s');
$qb->where("sc.date =:date and s.id=:student ");
$qb->setParameter('date',$date);
#$qb->setParameter('time',$time);
$qb->setParameter('student',$student);
$today = $qb->getQuery()->getResult();


        return $this->render('counsling/fromStudentBoocked.html.twig', array(
            'teachers'=>$teachers,
            'next'=>$nextcounslings,
            'prev'=>$prevcounslings,
            'today'=>$today
        ));
    }
    
    /**
     * @Route("/all/admin",name="admin_counsling")
     */

     public function admincounsling(StudentConslingRepository $studentConslingRepository){

        $counslings=$studentConslingRepository->findAll();

        return $this->render('counsling/admincounsling.html.twig', array(
            'counslings'=>$counslings
        ));
     }

}
