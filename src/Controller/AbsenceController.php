<?php

namespace App\Controller;

use App\Entity\EducationGroup;
use App\Entity\Seance;
use App\Entity\StudentAbsent;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\TeachersAbsent;
use App\Entity\User;
use App\Form\StudentType;
use App\Repository\AbsenceRepository;
use App\Repository\EducationGroupRepository;
use App\Repository\SeanceRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Route("/absent")
 */
class AbsenceController extends Controller{

    /**
     * display all teachers and their absence status
     * @Route("/teacher/{classeId}/{seanceId}/{date}", name="absence_index" ,methods={"GET"})
     */
    public function indexAction($classeId,$seanceId,$date)
    {
       
$em=$this->getDoctrine()->getManager();
$seance=$em->getRepository("App:Seance")->findOneBy(array('id'=>$seanceId,'educationgroup'=>$classeId));
$teachers=$seance->getTeacher();

$currentDate=new \DateTime();
$currentDate=$currentDate->format('Y-m-d');
$absent=$em->getRepository("App:TeachersAbsent")->findOneBy(array("teacher"=>$teachers,"date"=>$date,'seance'=>$seanceId));
if(($absent==null)||($absent->getStatus()==1)){
    $status=1;
}else{
    $status=0;
}

$seancedate=$seance->getDate();
        $seancedate=new \DateTime($seancedate);
        $nowdate=new \DateTime();
$nbrSeanceTot=$this->nombreSeance($seancedate,$nowdate,$seanceId);

$AbTeachers=array();
$result=$em->getRepository("App:TeachersAbsent")->findBy(array('seance'=>$seanceId,'teacher'=>$teachers,'status'=>0));
$nbrAb=count($result);
//dump($nbrAb);
 if($nbrSeanceTot!=0)
$AbTeachers[$teachers->getId()]=array('nbrAb'=>$nbrAb,'pourcentageAb'=>$nbrAb*100/$nbrSeanceTot);
else
    $AbTeachers[$teachers->getId()]=array('nbrAb'=>0,'pourcentageAb'=>0);

        return $this->render('absence/index.html.twig', array(
            
            'teacher' => $teachers,
            'nbrTotalSeance'=>$nbrSeanceTot,
            'absentTeachers'=>$AbTeachers,
            'status'=>$status

        ));
    }

    /**
     * find and display all seances of a classe
     * @param $id 
     * @return Response
     * @Route("/student/{id}", name="etudiant_seance")
     */
    public function studentSeanceAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $connectedTeacher=$this->getUser();
        $teacher=$this->getUser()->getTeacher();
         
     
        $classe=$em->getRepository("App:EducationGroup")->find($id);
         $status=$this->getUser()->getStatus();
        $organizedArray=array();
        for($i=1;$i<8;$i++){
            if($status=="Teacher"){
                $seance=$em->getRepository("App:Seance")->findBy(array("educationgroup"=>$id,"teacher"=>$teacher[0]->getId()));
            }else{
            $seance=$em->getRepository("App:Seance")->findBy(array("educationgroup"=>$id));
            }
        array_push($organizedArray,$seance);
        }

        $begin=[];
        $end=[];
        $namee=[];
        $org=[];
  foreach($seance as $s){
        $date=$s->getDate();
        $id=$s->getId();
        $begint=$s->getBeginTime();
        $begint=date('H:i',strtotime($begint));
        $endt=$s->getEndTime();
        $endt=date('H:i',strtotime($endt));
        $begint=date('Y-m-d H:i',strtotime("$date $begint"));
       // dump($begint);
        $endt=date('Y-m-d H:i',strtotime("$date $endt "));
        $cat=$s->getCategory();
        $group=$s->getEducationgroup()->getName();
        $name=$group;
        array_push($org,$id);
       // dump($org);
       array_push($begin,$date);
        array_push($namee,$name);
        array_push($end,$endt);
      //  dump($begin);
        //dump($namee);
        //dump($end);
  }
  $day=[];
  $nb=0;
  $nbr=[];
  $i=0;
foreach( $seance as $sed){
    $day[]=$sed->getDay();
    

$nbr[$i]=$nb;
   $i=$i+1;
}
//dump($nbr);

        return $this->render('absence/listSeance.html.twig', array(
            'seances' => $seance,
            'classe'=>$classe,
            'begin'=>$begin,
            'end'=>$end,'name'=>$namee,
            'event'=>$org,
            'day'=>$day,
            'nbrs'=>$nbr
        ));

    }

    /**
     * display students by classe and their absence status
     * @param $classeId
     * @param $seanceId
     * @return Response
     * @Route("/student/{classeId}/{seanceId}/{date}", name="etudiant_detail")
     */
    public function detailStudentAction($classeId,$seanceId,$date)
    {
       

        $em=$this->getDoctrine()->getManager();
        $education=$em->getRepository("App:EducationGroup")->findOneBy(['id'=>$classeId]);
        $students=$education->getStudents();
        $seance=$em->getRepository("App:Seance")->findOneBy(array("id"=>$seanceId));
        $absents=array();
        $dat=$seance->getDate();
        $date=new \DateTime($date);

        $beginDate=$seance->getDate();
        $beginDate=new \DateTime($beginDate);
     //   dump($beginDate);
        $endDate=$seance->getDateend();
        $endDate=new \DateTime($endDate);
        //dump($endDate);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($beginDate, $interval, $endDate);

        foreach ($students as $student){
            $studentAbsent=$em->getRepository("App:StudentAbsent")->findOneBy(array("student"=>$student,"date"=>$date,"seance"=>$seanceId));
            if($studentAbsent==null){
                $absents[$student->getId()]=true;
                $studentAbsent=$em->getRepository("App:StudentAbsent")->findOneBy(array("student"=>$student,"date"=>$date,"seance"=>$seanceId),array('date'=>'DESC'));
                /*if($studentAbsent!=null){
                    $absents[$student->getId()]=$studentAbsent->getStatus();
                    $absents[$student->getId()]=false;
                }*/
            }
            else
                $absents[$student->getId()]=$studentAbsent->getStatus();
        }

        $days=$seance->getDay();
        $nbrday=count($days);
        $seancedate=$seance->getDate();
        $seancedate=new \DateTime($seancedate);
        $nowdate=new \DateTime();
      
     
        $nbrSeanceTot=$this->nombreSeance($seancedate,$nowdate,$seanceId);
        
        $absence=array();
        foreach ($students as  $s){
            $studentAbs=$em->getRepository("App:StudentAbsent")->findBy(array('student'=>$s->getId(),'seance'=>$seanceId));
           //dump($studentAbs);
            if (count($studentAbs)==0){
                $absence[$s->getId()]=array('nbrAb'=>0,'pourcentageAb'=>0);
            }else{
                $status=0;
                $bool=-1;
                $nbrSeanceAbst=0;
                foreach ($studentAbs as $studentAb){
                    if(($studentAb->getStatus()==1)){
                        $bool=0;
                        $status=1;
                        $beginDate=new \DateTime($studentAb->getDate()->format('Y-m-d')." ".$studentAb->getbeginTime()->format('H:i:s'));
                    }
                    if(($studentAb->getStatus()==0)){
                        $bool=1;
                        $status=0;
                        $endDate=new \DateTime($studentAb->getDate()->format('Y-m-d')." ".$studentAb->getbeginTime()->format('H:i:s'));
                        $nbrSeanceAbst+=1;
                    }
                }
                if ($nbrSeanceTot != 0)
                $absence[$s->getId()]=array('nbrAb'=>$nbrSeanceAbst,'pourcentageAb'=>$nbrSeanceAbst*100/$nbrSeanceTot);
                else
                $absence[$s->getId()]=array('nbrAb'=>0,'pourcentageAb'=>0);


            }}

        return $this->render('absence/etudiant.html.twig', array(
            'absences'=>$absence,
            'students' => $students,
            'absents'=>$absents,
            'date'=>$date,
            'nbrSeanceTot'=>$nbrSeanceTot,
        ));
    }

    private function nombreSeance($beginDate, $endDate,$seanceId)
    {
        $em=$this->getDoctrine()->getManager();
        $seance=$em->getRepository("App:Seance")->findOneBy(array('id'=>$seanceId));
        $days=$seance->getDay();
        $nbrday=0;
        foreach($days as $d){
            $nbrday=$nbrday+1;
            $day[]=$d->getId();
        }
        
        $interval = \DateInterval::createFromDateString('1 day');
       // dump($nbrday);
        $period = new \DatePeriod($beginDate, $interval, $endDate);
     //   dump($period);
        $nbrSeanceTot=0;
        $nbrSeance=0;
        foreach ( $period as $dt ){
            $date=$dt->format('Y-m-d ');
            $date=getdate(strtotime($date));
            $nbr=$em->getRepository("App:Seance")->findOneBy(array('id'=>$seanceId));
            $Seanceday=$nbr->getDay();
            if(in_array($date['wday'],$day)){
                $nbrSeance=$nbrSeance+1;
            }
            //dump($nbrSeance);
            $nbrSeanceTot=$nbrSeance;
        }
        return $nbrSeanceTot;
    }

    /**
     * display classes and number of student for each class
     * @Route("/student", name="absence_etudiants_index",methods={"GET"})
     */
    public function studentIndexAction()
    {
        $connectedTeacher=$this->getUser();
        $em=$this->getDoctrine()->getManager();
        $time=new \DateTime();
        

        $classes=array();
        if($this->isGranted("ROLE_SUPER_ADMIN")){
            $classes=$em->getRepository("App:EducationGroup")->findAll();
        }elseif ($this->isGranted("ROLE_ADMIN")){
            $teacher=$this->getUser()->getTeacher();
            $seances=$em->getRepository("App:Seance")->findBy(array("teacher"=>$teacher[0]->getId()));
            foreach ($seances as $s){
                if(!in_array($s->getEducationgroup(),$classes))
                array_push($classes,$s->getEducationgroup());
            }
        }

        $stat=array();
        
        foreach ($classes as $c){
            $clasid=$c->getId();
            $stat[$c->getId()]=count($c->getStudents());
        }

        return $this->render('absence/AbsenceEtudiant.html.twig', array(
            'classes' => $classes,
            'stat'=>$stat,

        ));
    }

    /**
     * set absence status for teachers
     * @param Request $request
     * @return Response
     * @Route("/teacher/absent/{date}", name="agents_teachers_absent")
     */
    public function teachersAbsentAction($date,Request $request)
    {
        $id=$request->get('id');
        $seanceId=$request->get('seanceId');
        $currentDate=new \DateTime();
        $currentDate=$currentDate->format("Y-m-d");
        $em=$this->getDoctrine()->getManager();
        $seance=$em->getRepository("App:Seance")->find($seanceId);
        $teacher=$em->getRepository("App:Teacher")->find($id);
      //  $date=new DateTime($date);
        $absent=$em->getRepository("App:TeachersAbsent")->findOneBy(array("teacher"=>$teacher,"date"=>$date,'seance'=>$seanceId));
        
        if($absent==null){
            $absentObj=new TeachersAbsent();
            $absentObj->setTeacher($teacher);
            $absentObj->setDate($date);
            $absentObj->setSeance($seance);
            $absentObj->setStatus(false);
            $em->persist($absentObj);
            $em->flush();
        }else{
            if($absent->getStatus()==true){
                $absent->setStatus(false);
            }else{
                $absent->setStatus(true);
            }
            $em->flush();
        }

        return new Response();
    }

     /**
     * set absence status for students
     * @param Request $request
     * @return Response
     * @Route("/std/absent/{date}", name="agents_students_absent")
     */
    public function studentsAbsentAction($date,Request $request,\Swift_Mailer $mailer)
    {
        $studentId=$request->query->get('studentId');
        $seanceId=$request->query->get('seanceId');
        $status=$request->query->get('status');
        $currentDate=new \DateTime();
        $em=$this->getDoctrine()->getManager();
        $student=$em->getRepository("App:Student")->find($studentId);
        $seance=$em->getRepository("App:Seance")->find($seanceId);
        $beginDate=$seance->getBeginTime();
        $endDate=$seance->getEndTime();
        $date=new DateTime($date);
       
       
        //$date=$period[];
        $absent=$em->getRepository("App:StudentAbsent")->findOneBy(array("student"=>$student,"date"=>$date,"seance"=>$seance));
        if($absent==null){
            $beginTime=new \DateTime($seance->getBeginTime());
            $endTime=new \DateTime($seance->getEndTime());
            $absentObj=new StudentAbsent();
            $absentObj->setStudent($student);
            $absentObj->setSeance($seance);
            $absentObj->setDate($date);

            if($status=="0")
                $absentObj->setStatus(false);
            else
                $absentObj->setStatus(true);

            $absentObj->setBeginTime($beginTime);
            $absentObj->setEndTime($endTime);
            $em->persist($absentObj);
            $em->flush();
        }else{
            if($absent->getStatus()==true){
                $absent->setStatus(false);
                $content = $content=$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'absence'])?$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'absence'])->getContent(): "You are absent on ";
                $this->get('Emailing')->absenceEmail($student,$this->renderView(
                        'email/emailAbsence.html.twig',
                        ["content" => $content,"date"=>$date->format("Y-m-d")]),$mailer);
            }else{
                $absent->setStatus(true);
            }
            $em->flush();
        }

        return new Response();
    }

    /**
     * list of classes for teachers absent
     * @return Response
     * @Route("/teacher", name="absence_enseignant_index")
     */
    public function listClasseAgentAction()
    {
        $em=$this->getDoctrine()->getManager();

        $teachers=$em->getRepository("App:Teacher")->findAll();

        $classes=$em->getRepository("App:EducationGroup")->findAll();
        $stat=array();
        #var_dump($classes);
        foreach ($classes as $c){
            #var_dump($c->getId());
            $stat[$c->getId()]=count($em->getRepository("App:Student")->findBy(array("classe"=>$c)));
           # var_dump($em->getRepository("App:Student")->findBy(array("classe"=>$c->getId())));
        }


        return $this->render("enseignantAbsence/listClass.html.twig",array(
            'classes'=>$classes,
            'stat'=>$stat,
            'teachers'=>$teachers
            ));
    }

    /**
     * @Route("/teacher/{id}", name="enseignant_seance")
     */
    public function listSeanceAgentAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $classe=$em->getRepository("App:EducationGroup")->find($id);
        $organizedArray=array();
        
                $seances=$em->getRepository("App:Seance")->findBy(array("educationgroup"=>$id));
                $day=[];
                foreach( $seances as $sed){
                    $day[]=$sed->getDay();
                    
                   
                }
       
      $begin=[];
      $end=[];
      $namee=[];
      $org=[];
foreach($seances as $s){
      $date=$s->getDate();
      $id=$s->getId();
      $begint=$s->getBeginTime();
      $begint=date('H:i',strtotime($begint));
      $endt=$s->getEndTime();
      $endt=date('H:i',strtotime($endt));
      $begint=date('Y-m-d H:i',strtotime("$date $begint"));
     // dump($begint);
      $endt=date('Y-m-d H:i',strtotime("$date $endt "));
      $cat=$s->getCategory();
      $group=$s->getEducationgroup()->getName();
      $name=$group;
      array_push($org,$id);
     // dump($org);
     array_push($begin,$date);
      array_push($namee,$name);
      array_push($end,$endt);
    //  dump($begin);
      //dump($namee);
      //dump($end);
}
        return $this->render('enseignantAbsence/listSeance.html.twig', array(
            'seances' => $seances,
            'classe'=>$classe,
           
            'begin'=>$begin,
            'end'=>$end,'name'=>$namee,
            'event'=>$org,
            'day'=>$day
        ));
    }
     /**
      * @Route("/rest",name="reset_teacher",methods={"DELETE"})
      */
      public function deleteteacher(Request $request): Response
      {

         $teacher=$request->get('selectteacher');

         if ($teacher== 'all'){
            $qbAll=$this->getDoctrine()->getManager()->createQueryBuilder();
            
            $qbAll->delete();
            $qbAll->from(TeachersAbsent::class,'a');
            $qbAll->getQuery()->execute();
            $this->addFlash("delete", "All absence has been resetted ");
         }
         else{
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->delete();
            $qb->from(TeachersAbsent::class, 'a');

            $qb->where("a.teacher =:teacher");
            $qb->setParameter('teacher',$teacher);
            $qb->getQuery()->execute();
            
            $this->addFlash("delete", "absence has been resetted ");
         }
         return $this->redirectToRoute('absence_enseignant_index');
      }

    /**
     * @Route("/list/all", name="list_absence")
     */
    public function listabsencestudent(){
         
        $em=$this->getDoctrine()->getManager();
        $studentabs = $em->getRepository("App:StudentAbsent")->findAll();
        foreach($studentabs as $abs){
            $d[]=$abs->getStatus();
        }
       // dump($studentabs);
        $teacherbs = $em->getRepository("App:TeachersAbsent")->findAll();
        $group=$em->getRepository('App:EducationGroup')->findAll();
            return $this->render('absence/absenceliststudents.html.twig', array(
                'studentabsent'=>$studentabs,
                'teacher'=>$teacherbs,
                'groups'=>$group
            ));
    } 

    /**
     * @Route("/filter/student" ,name="student_filter")
     */

     public function filters(Request $request){

        $em=$this->getDoctrine()->getManager();
        $group=$request->get('group');
        $month=$request->get('month');
        //dump($group);
       // dump($month);

      /*  $qbe = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbe->select('e');
        $qbe->from(EducationGroup::class, 'e');
        $qbe->where("e.name =:group");
        $qbe->setParameter('group',$group);
        $grp = $qbe->getQuery()->getResult();
        $grpid=$grp[0]->getId();*/

        
      
        if ($month!=null && $group!=null){
            $qbs = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbs->select('s');
        $qbs->from(Seance::class, 's');
        $qbs->where("s.educationgroup =:group and MONTH(s.date)=:month");
        $qbs->setParameter('group',$group);
        $qbs->setParameter('month',$month);
        $seances = $qbs->getQuery()->getResult();
           
    $result=[];
    foreach($seances as $seance)
    {
         $result=array_merge($result,$seance->getStudentAbsents()->getValues());
         
    }
     }
       elseif($month=null && $group!=null){
        $qbs = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbs->select('s');
        $qbs->from(Seance::class, 's');
        $qbs->where("s.educationgroup =:group ");
        $qbs->setParameter('group',$group);
        
        $seances = $qbs->getQuery()->getResult();
        foreach($seances as $seance)
        {
             $result=array_merge($result,$seance->getStudentAbsents()->getValues());
             
        }
           
       }
       elseif($month=!null && $group=null)
       {
        $qbs = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbs->select('s');
        $qbs->from(Seance::class, 's');
        $qbs->where(" MONTH(s.date)=:month");
        
        $qbs->setParameter('month',$month);
        $seances = $qbs->getQuery()->getResult();
        foreach($seances as $seance)
        {
             $result=array_merge($result,$seance->getStudentAbsents()->getValues());
             
        }
       }
     $teacherbs = $em->getRepository("App:TeachersAbsent")->findAll();
     $group=$em->getRepository('App:EducationGroup')->findAll();
     return $this->render('absence/absenceliststudents.html.twig', array(
        'studentabsent'=>$result,
        'teacher'=>$teacherbs,
        'groups'=>$group
        
    ));
    }


    /**
     * @Route("/filter/teacher" ,name="teacher_filter")
     */

    public function filtert(Request $request){

        $em=$this->getDoctrine()->getManager();
        $group=$request->get('groupt');
        $month=$request->get('montht');
        /*dump($group);
        dump($month);*/

      /*  $qbe = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbe->select('e');
        $qbe->from(EducationGroup::class, 'e');
        $qbe->where("e.name =:group");
        $qbe->setParameter('group',$group);
        $grp = $qbe->getQuery()->getResult();
        $grpid=$grp[0]->getId();*/

        
      
        if ($month!=null && $group!=null){
            $qbs = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbs->select('s');
        $qbs->from(Seance::class, 's');
        $qbs->where("s.educationgroup =:group and MONTH(s.date)=:month");
        $qbs->setParameter('group',$group);
        $qbs->setParameter('month',$month);
        $seances = $qbs->getQuery()->getResult();
           
    $result=[];
    foreach($seances as $seance)
    {
         $result=array_merge($result,$seance->getTeachersAbsents()->getValues());
         
    }
     }
       elseif($month=null && $group!=null){
        $qbs = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbs->select('s');
        $qbs->from(Seance::class, 's');
        $qbs->where("s.educationgroup =:group ");
        $qbs->setParameter('group',$group);
        
        $seances = $qbs->getQuery()->getResult();
        foreach($seances as $seance)
        {
             $result=array_merge($result,$seance->getTeachersAbsents()->getValues());
            
        }
           
       }
       elseif($month=!null && $group=null)
       {
        $qbs = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbs->select('s');
        $qbs->from(Seance::class, 's');
        $qbs->where(" MONTH(s.date)=:month");
        
        $qbs->setParameter('month',$month);
        $seances = $qbs->getQuery()->getResult();
        foreach($seances as $seance)
        {
             $result=array_merge($result,$seance->getTeachersAbsents()->getValues());
             
        }
       }
       $studentabs = $em->getRepository("App:StudentAbsent")->findAll();
     $group=$em->getRepository('App:EducationGroup')->findAll();
     return $this->render('absence/absenceliststudents.html.twig', array(
        'studentabsent'=>$studentabs,
        'teacher'=>$result,
        'groups'=>$group
        
    ));
    }


  
}
