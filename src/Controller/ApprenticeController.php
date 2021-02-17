<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Module;
use App\Entity\Student;
use App\Entity\Application;
use App\Entity\University;
use App\Entity\Seance;
use App\Repository\ModuleRepository;
use App\Repository\CategoryRepository;
use App\Repository\TypeCourseRepository;
use App\Repository\CandidatureDeadlineRepository;
use App\Repository\UniversityRepository;

class ApprenticeController extends Controller
{

    /**
     * @Route("/apprentice", name="apprentice")
     */
    public function apprentice(ModuleRepository $moduleRepository,TypeCourseRepository $typeCourseRepository, CategoryRepository $categoryReposetory,CandidatureDeadlineRepository $candidatureDeadlineRepository, UniversityRepository $universityRepository)
    {
        // Students dashboard action
        #$teacher = $this->getUser()->getTeacher();
        #$student = $this->getUser()->getStudent();
        $modules = [];
        #if (isset($teacher[0]))
         #   $modules = $moduleRepository->findBy(["teacher"=>$teacher[0]->getId()]);
        #else if(isset($student[0]))
         #   $modules = $moduleRepository->findBy(["teacher"=>$student[0]->getTeacher()->getId()]);
        #else 
            $modules = $moduleRepository->findBy(array(),array('id' => 'DESC'),1,1);
           # die(var_dump($modules));
            $typeCourse = $typeCourseRepository->findAll();
            $category = $categoryReposetory->findAll();
            $candidatureDeadline = $candidatureDeadlineRepository->findOneBy([],['id'=>'DESC'],0,1);
            $student = $this->getUser()->getStudent();
            $conn = $this->getDoctrine()->getManager()
            ->getConnection();
            $sql = 'SELECT MIN(u.satrange) as goal FROM university u,student s,application a WHERE a.student_id = s.id AND u.title = a.universityname AND s.id = "'.$student[0]->getId().'"';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $leastUniversityScore = $stmt->fetchAll();
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->select('u');
            $qb->from(University::class, 'u');
            $qb->join(Application::class, 'a');
            $qb->join(Student::class, 's');
            $qb->where('u.title = a.universityname and  a.student = s.id and s.id = :student');
            $qb->setParameter('student',$student[0]);
            $universites = $qb->getQuery()->getResult();
            usort($universites , function($a, $b) {
                $student = $this->getUser()->getStudent();
                $university1Ranges = explode('-',$a->getSatrange());
                $unversity1Score = (int)$university1Ranges[0];
                $a->setChance($student[0]->getCandidatures()->first()?((int)$student[0]->getCandidatures()->first()->getSatScore()/$unversity1Score)*100:0);
                $university2Ranges = explode('-',$b->getSatrange(),1);
                $unversity2Score = (int)$university2Ranges[0];
                $b->setChance($student[0]->getCandidatures()->first()?((int)$student[0]->getCandidatures()->first()->getSatScore()/$unversity2Score)*100:0);
                return $a->getChance() > $b->getChance() ? -1: 1;
            });
            if($candidatureDeadline!=null)
            $upcomingDeadline = $candidatureDeadline->getUpcomingDeadline();
            else 
            $upcomingDeadline = null;
            
            //Get next counsling date
            
            $sqlNextCounsling = 'SELECT c.date
FROM counsling c, student_consling sc
WHERE c.date > "'.date("Y-m-d").'" 
AND c.id = sc.counsling_id AND sc.student_id = "'.$student[0]->getId().'"
ORDER BY c.date ASC LIMIT 1;';
            /*var_dump($sqlNextCounsling);
            die();*/
            $stmtNextCounsling = $conn->prepare($sqlNextCounsling);
            $stmtNextCounsling->execute();
            $nextCounsolingSession = $stmtNextCounsling->fetchAll();
            //var_dump($candidatureDeadline->getAllDeadlines());
            //var_dump(get_object_vars($candidatureDeadline));
            //die (var_dump($universites));
            //die(var_dump($candidatureDeadline));
            /*$qb= $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->select('count(u.id)');
            $qb->from('App:University','u');

            $applicationCount = $qb->getQuery()->getSingleScalarResult();*/
            /*$qbSession = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qbSession->select('s');
        $qbSession->from(Seance::class, 's');
        $qbSession->where("s.date < :date");
        $qbSession->setParameter('date',$classeId);
        $previousSessions = $qbSession->getQuery()->getResult();     */
        $previousSession = null;
        $begin=[];
        $end=[];
        $namee=[];
        $org=[];
        $day=[];
        $seances=[];
        foreach ($student[0]->getEducationGroups() as $educationGroup){
            foreach ($educationGroup->getSeances() as $seance ) {
                $seances[]=$seance;
                $day[]=$seance->getDay();
                $date=$seance->getDate();
                $id=$seance->getId();
                $begint=$seance->getBeginTime();
                $begint=date('H:i',strtotime($begint));
                $endt=$seance->getEndTime();
                $endt=date('H:i',strtotime($endt));
                $begint=date('Y-m-d H:i',strtotime("$date $begint"));
               // dump($begint);
                $endt=date('Y-m-d H:i',strtotime("$date $endt "));
                $cat=$seance->getCategory();
                $group=$seance->getEducationgroup()->getName();
                $name=$group;
                array_push($begin,$date);
            array_push($namee,$name);
            array_push($end,$endt);
                if($seance->getDate()<date('Y-m-d') ){
                    $nearDate = $seance->getDate();
                    if ($seance->getDate()>=$nearDate)
                     $previousSession = $seance;
                }
            }
        }
        return $this->render('apprentice/index.html.twig', [
            'modules' => $modules,
            'typeCourse' => $typeCourse,
            'categories' => $category,
            'candidatureDeadline' => $candidatureDeadline,
            'leastUniversityScore' => $leastUniversityScore[0]?$leastUniversityScore[0]['goal']:null,
            'universities' => $universites,
            'upcomingDeadline'=>$upcomingDeadline,
            'nextCounsolingSession'=>isset($nextCounsolingSession[0])?$nextCounsolingSession[0]['date']:'--',
            'session'=>$previousSession,
	    'seances'=>$seances,
            'day'=>$day,
            'name'=>$namee,
            //'applicationCount' => $applicationCount,
            //'test'=>$test,
        ]);
    
    }



    /**
     * @Route("/sat", name="sat")
     */
    public function sat()
    {

        return $this->render('sat.html.twig', [
           
        ]);

    }

}
