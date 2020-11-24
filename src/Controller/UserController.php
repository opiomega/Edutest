<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\PhotoType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    { 
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            
        ]);
    }

    
    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
            
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $userStatus = $user->getStatus();
        $edit = [true];
        $userRole = $this->getUser()?$this->getUser()->getRoles():[null];
        $form = $this->createForm(UserType::class, $user,["edit"=>$edit,'roles'=>$userRole]);
        $form->handleRequest($request);

        $teach=$this->getUser()->getTeacher();
        $stud=$this->getUser()->getStudent(); 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            if($userStatus =="Student")
            {
                return $this->redirectToRoute("student_index");
            }
            elseif($userStatus =="Teacher"){
                return $this->redirectToRoute("teacher_index");
            }
            
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
    /**
     * @Route("/{id}/editphoto", name="user_editphoto", methods={"GET","POST"})
     */
    public function editphoto(Request $request, User $user): Response
    {
        $userStatus = $user->getStatus();
        $edit = [true];
        $userid= $this->getUser()->getStudent();
        $student=$this->getUser()->getStudent();
         
        $teacher=$this->getUser()->getTeacher();
       
        $userRole = $this->getUser()?$this->getUser()->getRoles():[null];
        $form = $this->createForm(PhotoType::class, $user,["edit"=>$edit,'roles'=>$userRole]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $user->setPhotoFile(null);
           
        $this->addFlash("success", "Your have successfully changed the photo ");
            if($userStatus =="Student")
            {
                return $this->redirectToRoute("student_show",["id"=>$student[0]->getId()]);
            }
            elseif($userStatus =="Teacher"){
                return $this->redirectToRoute("teacher_show",["id"=>$teacher[0]->getId()]);
            }
            
        }

        return $this->render('user/editphoto.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/confirmation/registration", name="confirmation_registration", methods={"GET","POST"})
     */
    public function confirmationRegistration(Request $request)
    {
        //$email= $request->get('email');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->findOneBy(['id'=>$request->get('userId')]);
        $key = $request->get('key');
        $message = 'error';
        //die($key."===".md5($user->getId().'cofirmAccount'.$user->getEmail()));
        if ($user!=null && $key===md5($user->getId().'cofirmAccount'.$user->getEmail())){
            $user->setConfirmed(1);
            $em->flush();
            $message = "Request confirmed successfully.";
        }
        return $this->render('user/confirmInscription.html.twig',['message'=>$message]);
    }
   
}
