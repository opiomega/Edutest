<?php

namespace App\Controller;

use App\Entity\Profession;
use App\Entity\Student;
use App\Entity\User;
use App\Form\ProfessionType;
use App\Repository\ProfessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profession")
 */
class ProfessionController extends AbstractController
{
    /**
     * @Route("/", name="profession_index", methods={"GET"})
     */
    public function index(ProfessionRepository $professionRepository): Response
    {
        return $this->render('profession/index.html.twig', [
            'professions' => $professionRepository->findAll(),
        ]);
    }
    
    /**
     * @Route("/after/login", name="profession_after_login", methods={"GET"})
     */
    public function afterLogin(ProfessionRepository $professionRepository,Request $request): Response
    {
        $username=$request->get('username');
        $key=$request->get('key');
        $student = $this->getDoctrine()->getManager()->createQueryBuilder()->select('s')->from(Student::class, 's')->join(User::class,'u')->where('s.user = u.id and u.email = :username')->setParameter('username',$username)->getQuery()->getResult();
        if ($key!=md5($student[0]->getUser()->getEmail()))
            return $this->redirectToRoute("app_login");
        return $this->render('profession/afterLogin.html.twig', [
            'professions' => $professionRepository->findAll(),
            'username' => $username,
            'key'=>$key,
        ]);
    }

    /**
     * @Route("/new", name="profession_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $profession = new Profession();
        $form = $this->createForm(ProfessionType::class, $profession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profession);
            $entityManager->flush();

            return $this->redirectToRoute('profession_index');
        }

        return $this->render('profession/new.html.twig', [
            'profession' => $profession,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="profession_show", methods={"GET"})
     */
    public function show(Profession $profession): Response
    {
        return $this->render('profession/show.html.twig', [
            'profession' => $profession,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="profession_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Profession $profession): Response
    {
        $form = $this->createForm(ProfessionType::class, $profession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profession_index');
        }

        return $this->render('profession/edit.html.twig', [
            'profession' => $profession,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="profession_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Profession $profession): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$profession->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($profession);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profession_index');
    }
}
