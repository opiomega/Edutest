<?php
namespace App\Controller;

use App\Repository\CandidatureDeadlineRepository;
use App\Repository\EducationGroupRepository;
use App\Repository\SeanceRepository;
use App\Repository\StudentConslingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
class ProfessorController extends Controller
{
    
    /**
     * @Route("/professor", name="professor")
     */
    public function professor(StudentConslingRepository $studentConslingRepository,CandidatureDeadlineRepository $candidatureDeadlineRepository,SeanceRepository $seanceRepository,EducationGroupRepository $educationGroupRepository)
    {
         
        $teacher=$this->getUser()->getTeacher();
        $seances=$seanceRepository->findBy(['teacher'=>$teacher[0]]);
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
        }
        
        $zero=0;$one=0;$two=0;
        $counval=[$zero,$one,$two];
        $educations=$educationGroupRepository->findBy(['teacher'=>$teacher[0]]);
        $students=[];
        $totalstd=0;
        $submit=0;
        foreach($educations as $g){
           $std=$g->getStudents()->getValues();
           $students[]=$std;
           $totalstd=$totalstd+count($std);
           foreach($std as $st){
               $couns=$st->getStudentConslings()->getValues();

               $countcouns=count($couns);
                if($countcouns==0){
                 $zero=$zero+1;
                 $counval[0]=$zero;}
                if($countcouns==1){
                 $one=$one+1;
                 $counval[1]=$one;}
                if($countcouns==2){
                  $two=$two+1;
                  $counval[2]=$two;
                }
               /* $candidature=$st->getCandidatures()->getValues();
                  foreach($candidature as $c){
                   $candisub=$c->getIsSubmited();

                  if($candisub==1)
                    $submit=$submit+1;
                  }

                 dump($candidature);*/
                
               
           }
          // 
        }
	if ($submit!=0)
        $subperc=$submit*100/$totalstd;
	else 
	$subperc=0;
        //dump($counval);
        //dump($students);
        $conn = $this->getDoctrine()->getManager()
            ->getConnection();
        $sqlNextCounsling = 'SELECT c.date ,c.id,c.sbegin
FROM student_consling c
WHERE c.date > "'.date("Y-m-d").'" AND c.teacher_id="'.$teacher[0]->getId().'"
ORDER BY c.date ASC LIMIT 1;';
            /*var_dump($sqlNextCounsling);
            die();*/
            $stmtNextCounsling = $conn->prepare($sqlNextCounsling);
            $stmtNextCounsling->execute();
            $nextCounsolingSession = $stmtNextCounsling->fetchAll();
    
        
        
            $previousSession = null;
        foreach ($teacher[0]->getEducationGroups() as $educationGroup){
            foreach ($educationGroup->getSeances() as $seance ) {
                if($seance->getDate()>date('Y-m-d') ){
                    $nearDate = $seance->getDate();
                    if ($seance->getDate()>=$nearDate)
                     $previousSession = $seance;
                }
            }
        }
      
        $candidatureDeadline = $candidatureDeadlineRepository->findOneBy([],['id'=>'DESC'],0,1);

if($candidatureDeadline!=null)
$upcomingDeadline = $candidatureDeadline->getUpcomingDeadline();
else 
$upcomingDeadline = null;
          
    $counslings=$studentConslingRepository->findBy(['teacher'=>$teacher[0]]);
$datearray=[];
    foreach($counslings as $counslin){
        $counsdate=$counslin->getDate();
        $counsdate=$counsdate->format('m');
        
        $datearray[]=$counsdate;
        
    }

    $year=new \DateTime();
$year=$year->format('Y');
//dump($year);
$connex = $this->getDoctrine()->getManager()->getConnection();
        $tid=$teacher[0]->getId();
$sql = "SELECT   student_id , count( id ) 
FROM      student_consling
WHERE      student_consling.teacher_id= $tid
GROUP BY  student_id";
$stmt = $connex->prepare($sql);

$stmt->execute();
$resultdate=$stmt->fetchAll();
//dump($resultdate);
if ($nextCounsolingSession!=null){
$studentcouns=$studentConslingRepository->findOneBy(['id'=>$nextCounsolingSession[0]['id']]);
//dump($studentcouns);

$stdbook=$studentcouns->getStudent();
//dump($stdbook);
}
else {
    $stdbook=null;
}
$day=[];
foreach( $seances as $sed){
    $day[]=$sed->getDay();
}
   
        return $this->render('professor/index.html.twig',array(
            'begin'=>$begin,
            'end'=>$end,'name'=>$namee,
            'event'=>$org,
            'students'=>$students,'groups'=>$educations,
            'nextCounsolingSession'=>$nextCounsolingSession,
            'counsstudent'=>$stdbook,
            'session'=>$previousSession,
            'upcomingDeadline'=>$upcomingDeadline,
            'result'=>$counval,'year'=>$year,
            'totlastudents'=>$totalstd,'submit'=>$subperc,
            'seances'=>$seances,
            'day'=>$day
        ));
    }
}
