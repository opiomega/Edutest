<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ForgotPasswordType;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;

class ForgotPasswordController extends AbstractController
{
    /**
     * @Route("/forgot/password", name="forgot_password")
     */
    public function index( \Swift_Mailer $mailer,Request $request, UserRepository $userRepository)
    {
        $defaultData = ['password' => 'reset password'];
        $form = $this->createFormBuilder($defaultData)
            ->add('email', EmailType::class,["attr"=>["placeholder"=>"Your email please",'class' => "form-control"]])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $email = $data["email"];
        //die(var_dump($email));
        $user = $userRepository->findBy(["email"=>$email]);
        if (isset($user[0])){
            $message = (new \Swift_Message('Password Email'))
            ->setFrom('sami.maazaoui.adictest@gmail.com')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'forgot_password/emailContent.html.twig',
                     ["user" => $user[0],"key"=> md5($email)]
                ),
            'text/html'
            );

        // you can remove the following code if you don't define a text version for your emails
            /*->addPart(
                $this->renderView(
                    // templates/emails/registration.txt.twig
                    'forgot_password/registration.txt.twig'//,
                    //['name' => $name]
                ),
                'text/plain'
            )
        ;*/

            $mailer->send($message);
            
            return $this->render('forgot_password/index.html.twig', [
                'controller_name' => 'ForgotPasswordController',
            ]);
        }
            
        else {
           $form->get('email')->addError(new FormError('Email not found'));
        }
        
        }
        return $this->render('forgot_password/email.html.twig', [
             'controller_name' => 'ForgotPasswordController',
             'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/resetPassword/{id}", name="reset_password", methods={"GET","POST"})
     */
    public function resetPasswordAction (Request $request,User $user,UserPasswordEncoderInterface $passwordEncoder) {
        if($request->get('key')==md5($user->getEmail())){
        $form = $this->createForm(ForgotPasswordType::class, $user);
        $form->handleRequest($request);
        $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_login');
            
        }

        return $this->render('forgot_password/resetPasswordForm.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
        }
        else {
            throw new \Exception("Access denied");
        }
    }
}
