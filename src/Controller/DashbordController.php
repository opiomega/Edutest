<?php
namespace App\Controller;

use App\Entity\CandidatureDeadline;
use App\Entity\Category;
use App\Entity\Student;
use App\Entity\Test;
use App\Entity\TestScore;
use App\Entity\User;
use App\Repository\CandidatureDeadlineRepository;
use App\Repository\CandidatureRepository;
use App\Repository\SeanceRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
class DashbordController extends Controller
{
    
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(SeanceRepository $seanceRepository,StudentRepository $studentRepository,UserRepository $userRepository ,CandidatureRepository $candidatureRepository,CandidatureDeadlineRepository $candidatureDeadlineRepository)
    {    
        $name=[];
        $begin=[];
        $end=[];
        $seances=$seanceRepository->findAll();

        foreach($seances as $s){
            $date=$s->getDate();
            $start=$s->getBeginTime();
            $fin=$s->getEndTime();
            $na=$s->getEducationgroup()!==null?$s->getEducationgroup()->getName():null;

            $start=date('H:i',strtotime($start));
            $fin=date('H:i',strtotime($fin));
            $startdate=date('Y-m-d H:i',strtotime("$date $start"));
            $enddate=date('Y-m-d H:i',strtotime("$date $fin"));
            $begin[]=$startdate;
            $end[]=$enddate;
            $name[]=$na;

        }
        $qb=$this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('s');
        $qb->from(Student::class, 's');
        $qb->join(User::class, 'u');
        $qb->where("s.levelTest = '1' and s.user = u.id and u.active = '1'");
        $student = $qb->getQuery()->getResult();

        $qbs = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbs->select('s');
        $qbs->from(Student::class, 's');
        $qbs->where("s.nextpaymentday is not null");
        
        $students = $qbs->getQuery()->getResult();

        $enrolleduser=$userRepository->findby(['active'=>1,'status'=>'Student']);
         $ids=[];

        foreach($enrolleduser as $enr){

            $ids[]=$enr->getId();

        }

       // dump($ids);

        $enrolledstudent=[];

        foreach($student as $std){

            $id=$std->getUser()->getId();

            //dump($id);

            if (array_search($id,$ids)){

                $enrolledstudent[]=$std;

                

            }



        }

          if($enrolledstudent!= null)

          $enrolledc=count($enrolledstudent);

          else

          $enrolledc=0;
       // dump($begin);
       // dump($end);
       // dump($name);
$year=new \DateTime();
$year=$year->format('Y');
//dump($year);
$conn = $this->getDoctrine()->getManager()->getConnection();
        
$sql = "SELECT   MONTH( student.createdat ) AS m, count( id ) 
FROM      student
WHERE     YEAR(student.createdat) = '$year' AND student.level_test= 1
GROUP BY  m";
$stmt = $conn->prepare($sql);

$stmt->execute();
$result=$stmt->fetchAll();
//dump($result);
$tn='SAT';

        $qbsc = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbsc->select('ts');
        $qbsc->from(TestScore::class, 'ts');
        $qbsc->join(Test::class, 't');
        $qbsc->where('ts.value BETWEEN 1500 and 1600');
        $qbsc->andwhere('t.id=ts.test  and t.title=:sat');

        $qbsc->setParameter('sat',$tn);
        $scorefirst = $qbsc->getQuery()->getResult();
        $scorefirst=count($scorefirst);

        $qbsc = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbsc->select('ts');
        $qbsc->from(TestScore::class, 'ts');
        $qbsc->join(Test::class, 't');
        $qbsc->where('ts.value BETWEEN 1300 and 1499');
        $qbsc->andwhere('t.id=ts.test  and t.title=:sat');

        $qbsc->setParameter('sat',$tn);
        $scoresec = $qbsc->getQuery()->getResult();
        $scoresec=count($scoresec);

        $qbsc = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbsc->select('ts');
        $qbsc->from(TestScore::class, 'ts');
        $qbsc->join(Test::class, 't');
        $qbsc->where('ts.value BETWEEN 1100 and 1299');
        $qbsc->andwhere('t.id=ts.test  and t.title=:sat');

        $qbsc->setParameter('sat',$tn);
        $scoreth = $qbsc->getQuery()->getResult();
        $scoreth=count($scoreth);
        //dump($scoreth);
        //dump($scoresec);

 //dump($scorefirst);

 $allcandi=$candidatureRepository->findAll();
 $allcandi=count($allcandi);
 $candisbu=$candidatureRepository->findBy(['isSubmited'=>1]);
 $candisbu=count($candisbu);
if ($candisbu!=0)
 $candidaperc=$candisbu*100/$allcandi;
 else
 $candidaperc=0;

 $term=$studentRepository->findBy(['term'=>1]);
 $term=count($term);
$arr=[];
$candidatureDeadline = $candidatureDeadlineRepository->findOneBy([],['id'=>'DESC'],0,1);

if($candidatureDeadline!=null)
$upcomingDeadline = $candidatureDeadline->getUpcomingDeadline();
else 
$upcomingDeadline = null;

//Get next counsling date

$day=[];
foreach( $seances as $sed){
    $day[]=$sed->getDay();
    
   
}



        return $this->render('admin/index.html.twig',array(
            'name'=>$name,
            'begin'=>$begin,
            'end'=>$end,
            'students'=>$students,'enrolled'=>$enrolledc,
            'result'=>$result,'firstsc'=>$scorefirst,'secsc'=>$scoresec,'thsc'=>$scoreth,
            'year'=>$year,
            'candida'=>$candidaperc,'term'=>$term,
            'upcomingDeadline'=>$upcomingDeadline,
            'seances'=>$seances,
            'day'=>$day
        ));
    }
}
