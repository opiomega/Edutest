<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Test;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\RouterInterface;
 
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;
    private $router;
    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(RouterInterface $router ,EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }
        else if ($this->checkCredentials($credentials,$user)) {
            if ($user->getStatus()=='Teacher'){
                $teacher = $user->getTeacher();
                if (!isset($teacher[0]))
                    throw new CustomUserMessageAuthenticationException('Incompleated teacher informaions !');
            }
            
            if ($user->getStatus()=='Student'){
                $student = $user->getStudent();
                if (!isset($student[0]))
                    throw new CustomUserMessageAuthenticationException('Incompleated student informaions !');
            }
            
            $registrationConfirmation = $user->getConfirmed();
            if (!$registrationConfirmation)
                    throw new CustomUserMessageAuthenticationException("You must confirm registration. Check your email please.");
            
            
            $userActive = $user->getActive();
        
            if ($userActive==0){
		if(isset($student[0])){
                    $levelTest = $student[0]->getLevelTest();
                $levelTestType =  $student[0]->getLevelTestType();
                
                $contract=$this->entityManager->getRepository('App:Contract')->findOneBy([],['id' => 'DESC']);
                //die(var_dump($contract));
                if ($contract === null)
                    throw new CustomUserMessageAuthenticationException("Contract in progress. You can contact our team.");
                
                if (!$levelTestType){
                    throw new CustomUserMessageAuthenticationException("Sorry there is no level test affected to you yet. You can contact our team!");
                }
		$levelTestData = $this->entityManager->getRepository(Test::class)->findBy(['type'=>'level','category'=>$student[0]->getLevelTestType()]);
                 if (!isset($levelTestData[0]) || $levelTestData[0]->getQuestions()->isEmpty()){
                    throw new CustomUserMessageAuthenticationException('Sorry there is no level test in our database yet. You can contact our team!');
                }

                if (!$levelTest){
                    //throw new CustomUserMessageAuthenticationException("Your account is not active to activate your account you must pay the placement test fee !");
                    throw new CustomUserMessageAuthenticationException("This user didin't pass the exam !");
                }
                
                /*$access=$student[0]->getAccess();
                if($access == 0 ){*/
                    throw new CustomUserMessageAuthenticationException("Your account is not active to activate your account you must pay the courses fees !");
                //}
                }
                throw new CustomUserMessageAuthenticationException('This user is not active !');
            }
            
            /*$student = $user->getStudent();
            if (isset($student[0])){
                $levelTest = $student[0]->getLevelTest();
                $levelTestType =  $student[0]->getLevelTestType();
                if (!$levelTestType){
                    throw new CustomUserMessageAuthenticationException("Sorry there is no level test affected to you yet. You can contact our team!");
                }
                if (!$levelTest){
                    throw new CustomUserMessageAuthenticationException("This user didin't pass the exam !");
                }
                $access=$student[0]->getAccess();
                if($access == 0 ){*
                    //throw new CustomUserMessageAuthenticationException("This user didin't pay !");
                //}
            }*/
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        
        $roles = $token->getRoles();
       
        $rolesTab = array_map(function ($role) {
            return $role->getRole();
        }, $roles);

        if (in_array('ROLE_ADMIN', $rolesTab, true)) {
            // c'est un aministrateur : on le rediriger vers l'espace teacher
            $redirection = new RedirectResponse($this->router->generate('professor'));
        }elseif (in_array('ROLE_SUPER_ADMIN', $rolesTab, true))
        {
            // c'est un utilisaeur lambda : on le rediriger vers l'espace admin
            $redirection = new RedirectResponse($this->router->generate('admin'));
        } else {
            // c'est un utilisaeur lambda : on le rediriger vers student
            $redirection = new RedirectResponse($this->router->generate('apprentice'));
        }

        return $redirection;
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}
