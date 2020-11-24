<?php
namespace App\Service;
/**
 * Description of Emailing
 *
 * @author sami
 */
class Emailing {
    public function documentDeadlineEmail($students,$candidatureDeadlines,$twig,$mailer,$object="Documents deadline") {
        //$sending = false;
        $curent = new \DateTime() ;
        $concernedStudents = [];
        foreach ($students as $student ){
        $candidatureDocuments = $student->getCandidatures()->first()?$student->getCandidatures()->first()->getAllDocuments():null;
        foreach ($candidatureDeadlines->getAllDeadlines() as $index=>$deadline){
            if ($deadline!=null){
                //var_dump($deadline);
                //var_dump($curent);
                $diff = $curent->diff($deadline,true)->days;
                if (($candidatureDocuments==null || $candidatureDocuments[$index]==null) and $diff <= 3){
                    if (!in_array($student,$concernedStudents))
                        $concernedStudents[] = $student;
                }
            }
        }
        }
        //var_dump($concernedStudents);
            if (isset($concernedStudents[0])){
            $message = (new \Swift_Message($object))
            ->setFrom('support@edu-test.co')
            ->setBcc($concernedStudents[0]->getUser()->getEmail())
            ->setBody(
                $twig,
            'text/html'
            );
            foreach ($concernedStudents as $index=>$student){
                if ($index!=0)
                    $message->addBcc($student->getUser()->getEmail());
            }
            $mailer->send($message);
            }
        
    }
     public function absenceEmail($student,$twig,$mailer,$object="Absence") {
        
            $message = (new \Swift_Message($object))
            ->setFrom('support@edu-test.co')
            ->setTo($student->getUser()->getEmail())
            ->setBody(
                $twig,
            'text/html'
            );
            
            $mailer->send($message);
    }
    public function levelTestEmail($student,$twig,$mailer,$object="Placement test") {
        
            $message = (new \Swift_Message($object))
            ->setFrom('support@edu-test.co')
            ->setTo($student->getUser()->getEmail())
            ->setBody(
                $twig,
            'text/html'
            );
            
            $mailer->send($message);
    }
    public function counslingEmail($educationgroup,$twig,$mailer,$object="Counseling confirmation") {
        
            $message = (new \Swift_Message($object))
            ->setFrom('support@edu-test.co')
            ->setTo($educationgroup->getTeacher()->getUser()->getEmail())
            ->setBody(
                $twig,
            'text/html'
            );
            $mailer->send($message);
            
            
            $messageStudents = (new \Swift_Message($object))
                    ->setFrom($educationgroup->getTeacher()->getUser()->getEmail())
                    ->setTo($educationgroup->getStudents()->first()->getUser()->getEmail())
                    ->setBody(
                    $twig,
                    'text/html'
                );
                foreach ($educationgroup->getStudents() as $index=>$student) {
                    if ($index!=0)
                        $messageStudents->addTo($student->getUser()->getEmail());
                }
            $mailer->send($messageStudents);      
    }
    public function teacherEmail($educationgroup,$twig,$mailer,$object="Teacher email",$teachersemail) {
        
            
                $messageStudents = (new \Swift_Message($object))
                    ->setFrom($educationgroup->getTeacher()->getUser()->getEmail())
                    ->setCc($teachersemail)
                    ->setBody(
                    $twig,
                    'text/html'
                );
                foreach ($educationgroup->getStudents() as $index=>$student) {
                        $messageStudents->addBcc($student->getUser()->getEmail());
                }
                $mailer->send($messageStudents);
            
    }
    public function adminEmail($students,$twig,$mailer,$object="Teacher email") {
        
        //var_dump($students);
                $messageStudents = (new \Swift_Message($object))
                    ->setFrom('hello@edu-test.co')
                    ->setBcc($students[0]->getUser()->getEmail())
                    ->setBody(
                    $twig,
                    'text/html'
                );
                foreach ($students as $index=>$student){
                    
                    if ($index!=0)
                    $messageStudents->addBcc($student->getUser()->getEmail());
                }
                $mailer->send($messageStudents);
            
    }
    
    public function adminEmailOnlyOneStudent($student,$twig,$mailer,$object="Teacher email") {
        
        //var_dump($students);
                $messageStudents = (new \Swift_Message($object))
                    ->setFrom('hello@edu-test.co')
                    ->setTo($student->getUser()->getEmail())
                    ->setBody(
                    $twig,
                    'text/html'
                );
                $mailer->send($messageStudents);
            
    }
}
