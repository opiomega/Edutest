<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Application;
use App\Form\CandidatureType;
use App\Repository\ApplicationRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notification;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\EducationGroup;

/**
 * @Route("/candidature")
 */
class CandidatureController extends AbstractController
{
    /**
     * @Route("/", name="candidature_index", methods={"GET"})
     */
    public function index(StudentRepository $studentRepository): Response
    {
        $student = $this->getUser()->getStudent();
        $teacher = $this->getUser()->getTeacher();
        if(isset($student[0])){
            
            $candidatures = $this->getDoctrine()
                ->getRepository(Candidature::class)
                ->findBy(['student'=>$student[0]->getId()]);
            if (isset($candidatures[0]))
                return $this->redirectToRoute('candidature_edit',["id"=>$candidatures[0]->getId()]);
            return $this->redirectToRoute('candidature_new');
        }
        else if (isset($teacher[0])){
            $candidatures = [];
            foreach ($teacher[0]->getEducationGroups() as $group){
                foreach ($group->getStudents() as $student){
                    $candidatures = array_merge($candidatures,$student->getCandidatures()->getValues());
                }
            }
            /*$student = $teacher[0]->getStudents();
            $ides = $student ->map(function($obj){return $obj->getId();})->getValues();*/
            //if (!$student->isEmpty()){
            /*$candidatures = $this->getDoctrine()
                ->getRepository(Candidature::class)
                ->findBy(['student'=>$student[0]->getId()]);*/
               /* $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb->select('c');
                $qb->from(Candidature::class, 'c');
                $qb->where($qb->expr()->in('c.student', $ides));
                $candidatures = $qb->getQuery()->getResult();
*/
                //$teacher=$this->getUser()->getTeacher();
                /*$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                        $qb->select('c');
                        $qb->from(Student::class, 's');
                        $qb->join(Teacher::class,'t');
                        $qb->innerJoin('s.educationGroups','t.educationGroups');
                        $qb->join(Candidature::class,'c');
                        $qb->where("t.id=:teacher and s.id=c.student");
                        $qb->setParameter('teacher',$teacher[0]);
                        $candidatures = $qb->getQuery()->getResult();*/
                /*$stduents = $studentRepository->findBy(["educationGroups"=>$teacher[0]->getEducationGroups()]);
                $candidatures = [];
                foreach($students as $student){
                    $candidatures = array_merge($candidatures,$student->getCandidatures()->getValues());
                }*/
            /*}
            else {
                $candidatures = null;
            }*/
        }
        else {
            $candidatures = $this->getDoctrine()
                ->getRepository(Candidature::class)
                ->findAll();
        }
        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }
    
    /**
     * @Route("/success", name="candidature_success", methods={"GET"})
     */
    public function success(): Response
    {
        
        return $this->render('candidature/success.html.twig');
    }

    /**
     * @Route("/new", name="candidature_new", methods={"GET","POST"})
     */
    
    public function new (Request $request,ApplicationRepository $applicationRepository): Response
    {
        
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class, $candidature,["fieldDisabled"=>false]);
        $form->handleRequest($request);
        $student= $this->getUser()->getStudent();
        $studentid=$student[0]->getId();
        $application=$applicationRepository->findBy(['student'=>$studentid]);
        $count=20-count($application);
        if ($form->isSubmitted() && $form->isValid()) {
            $formbanstatement=$form->get('bankstatmentFilepdf')->getData();   
            if ($formbanstatement != null)       
            $candidature->setBankStatementFile($formbanstatement);
             //first
            $formfirst=$form->get('transcriptfirstFilepdf')->getData();   
            if ($formfirst != null)       
            $candidature->setTranscriptFirstFile($formfirst);
            //second
            $formsecond=$form->get('transcriptsecondFilepdf')->getData();   
            if ($formsecond != null)       
            $candidature->setTranscriptSecondFile($formsecond);
            //third
            $formthird=$form->get('transcriptthirdFilepdf')->getData();   
            if ($formthird != null)       
            $candidature->setTranscriptThirdFile($formthird);
            //bac
            $formbac=$form->get('transcriptbacFilepdf')->getData();   
            if ($formbac != null)       
            $candidature->setTranscriptBacFile($formbac);
            //pass
            $formpass=$form->get('passportFilepdf')->getData();   
            if ($formpass != null)       
            $candidature->setPassportFile($formpass);
            //cin
            $formcin=$form->get('cinFilepdf')->getData();   
            if ($formcin != null)       
            $candidature->setCinFile($formcin);

            $student = $this->getUser()->getStudent();
            if (isset($student[0]))
                $candidature->setStudent($student[0]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();
            $this->addFlash("success", "New candidature created ");
            return $this->redirectToRoute('candidature_index');
        }

        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
            'applications'=>$application,
            'count'=>$count
        ]);
    }

    /**
     * @Route("/{id}", name="candidature_show", methods={"GET"})
     */
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }
    
    /**
     * @Route("/student/show", name="candidature_student_show", methods={"GET"})
     */
    public function studentShow(): Response
    {
        $candidature = $this->getUser()->getStudent()->first()->getCandidatures()->first();
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="candidature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Candidature $candidature,ApplicationRepository $applicationRepository): Response
    {
        if ($this->getUser()->getStatus()=="Student")
            $student= $this->getUser()->getStudent()->first();
        else
            $student=$candidature->getStudent();
        $studentid=$student->getId();
        $application=$applicationRepository->findBy(['student'=>$studentid]);
        $count=20-count($application);
        $edit = [true];
        $fieldDisabled = [false];
       
        if ($candidature->getIsSubmited())
            {$fieldDisabled = [true];}
        $form = $this->createForm(CandidatureType::class, $candidature, ["edit"=>$edit,"fieldDisabled"=>$fieldDisabled]);
        
        $form->handleRequest($request);
        $verif = $candidature->getAllFieldsFull();
        if ($form->isSubmitted() && $form->isValid()) {
            
            $formbanstatement=$form->get('bankstatmentFilepdf')->getData();   
            if ($formbanstatement != null)       
            $candidature->setBankStatementFile($formbanstatement);
             //first
            $formfirst=$form->get('transcriptfirstFilepdf')->getData();   
            if ($formfirst != null)       
            $candidature->setTranscriptFirstFile($formfirst);
            //second
            $formsecond=$form->get('transcriptsecondFilepdf')->getData();   
            if ($formsecond != null)       
            $candidature->setTranscriptSecondFile($formsecond);
            //third
            $formthird=$form->get('transcriptthirdFilepdf')->getData();   
            if ($formthird != null)       
            $candidature->setTranscriptThirdFile($formthird);
            //bac
            $formbac=$form->get('transcriptbacFilepdf')->getData();   
            if ($formbac != null)       
            $candidature->setTranscriptBacFile($formbac);
            //pass
            $formpass=$form->get('passportFilepdf')->getData();   
            if ($formpass != null)       
            $candidature->setPassportFile($formpass);
            //cin
            $formcin=$form->get('cinFilepdf')->getData();   
            if ($formcin != null)       
            $candidature->setCinFile($formcin);
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
                $form = null;
                $form = $this->createForm(CandidatureType::class, $candidature, ["edit"=>$edit,"fieldDisabled"=>$fieldDisabled]);
                $form->handleRequest($request);
            }
            $this->getDoctrine()->getManager()->flush();
         /* var_dump(mime_content_type('../public/uploads/candidature/transcriptsSecond/'.$candidature->getTranscriptSecond()));
            die("Bye");*/
            $this->addFlash("update", "Informations updated ");
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
            'applications'=>$application,
            'count'=>$count
        ]);
    }

    /**
     * @Route("/{id}", name="candidature_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Candidature $candidature): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$candidature->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidature);
            $entityManager->flush();
            $this->addFlash("delete", "Document deleted ");
        }

        return $this->redirectToRoute('candidature_index');
    }

    /**
     * @Route("/{id}/notification", name="candidature_show_by_notification", methods={"GET"})
     */
    public function showByNotification(Notification $notification): Response
    {
        $notification->setSeen(true);
        $this->getDoctrine()->getManager()->persist($notification);
        $this->getDoctrine()->getManager()->flush();
        $candidature = $notification->getCandidature();
        return $this->redirectToRoute('candidature_show',["id"=>$candidature->getId()]);
    }


    /**
* Create and download some zip documents.
*@Route("/zip/{id}", name="candidature_zip", methods={"GET"})
* 
* @return Symfony\Component\HttpFoundation\Response
*/
public function zipDownloadDocumentsAction(Candidature $candidature, Request $request)
{

    
    $studentfirst=$candidature->getStudent()->getFirstname();
    $studentlast=$candidature->getStudent()->getLastname();

    $lettremath=$candidature->getLetterOfRecommendationMath();
    $lettre=$candidature->getLetterOfRecommendation();
    $test=$candidature->getTest();
    $survey=$candidature->getSurvey();
    $transfirst=$candidature->getTranscriptFirst();
    $transsecond=$candidature->getTranscriptSecond();
    $trasnthird=$candidature->getTranscriptThird();
    $transbac=$candidature->getTranscriptBac();
    $cin=$candidature->getCin();
    $passport=$candidature->getPassport();
    $bank=$candidature->getBankStatement();
$files=[];

   // $files=[$passport,$cin,$transbac,$trasnthird,$transsecond,$transfirst,$survey,$test,$lettre,$lettremath];
    array_push($files, getcwd().'/uploads/candidature/bankStatements/'.$bank);
    array_push($files, getcwd().'/uploads/candidature/cin/'.$cin);
    array_push($files, getcwd().'/uploads/candidature/passport/'.$passport);
    array_push($files, getcwd().'/uploads/candidature/letterOfRecommendations/' . $lettre);
    array_push($files, getcwd().'/uploads/candidature/letterOfRecommendationsMath/' . $lettremath);
    array_push($files, getcwd().'/uploads/candidature/transcriptsBac/' . $transbac);
    array_push($files, getcwd().'/uploads/candidature/transcriptsFirst/' . $transfirst);
    array_push($files, getcwd().'/uploads/candidature/transcriptsSecond/' . $transsecond);
    array_push($files, getcwd().'/uploads/candidature/transcriptsThird/' . $trasnthird);
    array_push($files, getcwd().'/uploads/candidature/surveys/' . $survey);
    array_push($files, getcwd().'/uploads/candidature/tests/' . $test);

 $zip = new \ZipArchive();

    $zipname=$studentfirst.'_'.$studentlast.'_Application.zip';

    $zip->open($zipname, \ZipArchive::CREATE);
   
      
    foreach ($files as $file) {
        
        $zip->addFromString(basename($file),  file_get_contents($file));//add each file into example_zip zip file
    }
    
  
$zip->close();
if (headers_sent()) {
    echo 'HTTP header already sent';
} else {
    if (!is_file($file)) {
        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        echo 'File not found';
    } else if (!is_readable($zipname)) {
        header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
        echo 'File not readable';
    } else {
        header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: ".filesize($zipname));
        header("Content-Disposition: attachment; filename=\"".basename($zipname)."\"");
        readfile($zipname);
        exit;
    }
}

  
return $this->redirectToRoute('candidature_index');
}

 /**
     * @Route("/application/{id}", name="condapplication_new", methods={"GET","POST"})
     */
    public function newapplication(Request $request,ApplicationRepository $applicationRepository, $id): Response
    {
        
    
        
        $comp=$request->get('compteur');
        
            if($comp==""){
                $comp= 1;
            }
            
       for($i = 1 ; $i<= $comp;$i++){
        $student=$this->getUser()->getStudent();
        $studentid=$student[0]->getId();
        //var_dump($i);
        $uniname=$request->get('univer'.$i);
        
        $email=$request->get('email'.$i);
        $password=$request->get('pass'.$i);
        $application = new Application();
        $applicationn=$applicationRepository->findBy(['student'=>$studentid]);
        $count=count($applicationn);
        if ($count==20){
            $this->addFlash("del", "You have reached the maximum number of unviversities allowed to apply");
            return $this->redirectToRoute('candidature_edit',array('id'=>$id));
        }

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                        $qb->select('a');
                        $qb->from(Application::class, 'a');
                        $qb->join(Student::class,'s');
                       
                        $qb->where("UPPER(a.universityname) like UPPER(:university) and a.student= s.id and s.id = :student");
                        $qb->setParameter('university',$uniname);
                        $qb->setParameter('student',$this->getUser()->getStudent());
                        $result = $qb->getQuery()->getResult();
        if ($result){
            $this->addFlash("del", "Aleardy applied to ".$uniname."!!");
            return $this->redirectToRoute('candidature_edit',array('id'=>$id));
        }
        $application->setEmail($email);
        $application->setPassword($password);
        $application->setStudent($student[0]);
        $application->setUniversityname( $uniname);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($application);
        $entityManager->flush();
        $this->addFlash("suc", "Applied successfuly!! ");
        
       }
       
       return $this->redirectToRoute('candidature_edit',array('id'=>$id));
     
      
       
    }

}
