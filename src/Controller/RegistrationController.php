<?php
namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\LockedException;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,\Swift_Mailer $mailer,UserRepository $userReposetory)
    {
        
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $status = $user->getStatus() ;
            $user->setActive(0);
            $user->setConfirmed(0);
            if($status == "Student")
            {
                $user->setRoles(['ROLE_POWER_USER']);
            }
            else
            {
                $user->setRoles(['ROLE_ADMIN']);
            }
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $userEmail = $userReposetory->findBy(["email" => $form->get('email')->getData()]);
            if (isset($userEmail[0])) {
                $form->get('email')->addError(new FormError('email exist !!!'));
            }else {
            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = $user->getEmail();
            $message = (new \Swift_Message('Registration confirmation'))
            ->setFrom('sami.maazaoui.adictest@gmail.com')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'user/emailRegistration.html.twig',
                     ["user" => $user, "key"=>md5($user->getId().'cofirmAccount'.$user->getEmail())]
                ),
            'text/html'
            );
            
            $mailer->send($message);

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $userid = $user->getId();
            if($status == "Student")
            {
                return $this->redirectToRoute('student_new', array('id' => $userid));
                
            }
            else
            {
                return $this->redirectToRoute('teacher_new', array('id' => $userid));
            }
        }
    }
        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}