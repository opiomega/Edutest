<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\CandidatureDeadlineRepository;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils,CandidatureDeadlineRepository $candidatureDeadlineRepository,CandidatureRepository $candidatureRepository,\Swift_Mailer $mailer): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        //sending documents deadlines email
       
        $candidatureDeadline = $candidatureDeadlineRepository->findOneBy([],['id'=>'DESC'],0,1);
        if ($candidatureDeadline!=null){
            $content=$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'deadlines'])?$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'deadlines'])->getContent():"You must send all your documents";
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->select('s');
            $qb->from('App:Student', 's');
            $qb->join('App:User','u');
            $qb->where("s.user =u.id and u.active=1 ");
            $students = $qb->getQuery()->getResult();
            //$students = $this->getDoctrine()->getManager()->getRepository('App:Student')->findAll();
            //foreach ($students as $student) 
                $this->get('Emailing')->documentDeadlineEmail($students,$candidatureDeadline,$this->renderView(
                        'email/emailDeadline.html.twig',
                        ["content" => $content]
                    ),$mailer);
        }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error!=null && $error->getMessage()=="Incompleated teacher informaions !"){
            $user = $this->getDoctrine()->getManager()->getRepository("App:User")->findBy(['email'=>$authenticationUtils->getLastUsername()]);
            return $this->redirectToRoute('teacher_new', array('id' => $user[0]->getId()));
        }
        if ($error!=null && $error->getMessage()=="Incompleated student informaions !"){
            $user = $this->getDoctrine()->getManager()->getRepository("App:User")->findBy(['email'=>$authenticationUtils->getLastUsername()]);
            return $this->redirectToRoute('student_new', array('id' => $user[0]->getId()));
        }
        if ($error!=null && $error->getMessage()=="This user didin't pass the exam !"){
            $key = md5($authenticationUtils->getLastUsername());
            return $this->redirectToRoute('pass_level_test',['username'=>$authenticationUtils->getLastUsername(),'key'=>$key]);
        }
        if ($error!=null && $error->getMessage()=="This user didin't pay !"){
            return $this->redirectToRoute('denied',['username'=>$authenticationUtils->getLastUsername()]);
        }
        if ($error!=null && $error->getMessage()=="Sorry there is no level test affected to you yet. You can contact our team!"){
            $key = md5($authenticationUtils->getLastUsername());
            return $this->redirectToRoute('profession_after_login',['username'=>$authenticationUtils->getLastUsername(),'key'=>$key]);
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
