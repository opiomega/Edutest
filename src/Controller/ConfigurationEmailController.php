<?php

namespace App\Controller;

use App\Entity\ConfigurationEmail;
use App\Form\ConfigurationEmailType;
use App\Repository\ConfigurationEmailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/configuration/email")
 */
class ConfigurationEmailController extends AbstractController
{
    /**
     * @Route("/", name="configuration_email_index", methods={"GET"})
     */
    public function index(ConfigurationEmailRepository $configurationEmailRepository): Response
    {
        return $this->render('configuration_email/index.html.twig', [
            'configuration_emails' => $configurationEmailRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="configuration_email_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $configurationEmail = new ConfigurationEmail();
        $form = $this->createForm(ConfigurationEmailType::class, $configurationEmail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $email =  $entityManager->getRepository('App:ConfigurationEmail')->findBy(['subject'=>$form->getData()->getSubject()]);
            if (!isset($email[0])){
                $entityManager->persist($configurationEmail);
                $entityManager->flush();
            }
            else {
                $this->addFlash('danger','This email configuration exist');
            }
            return $this->redirectToRoute('configuration_email_index');
        }

        return $this->render('configuration_email/new.html.twig', [
            'configuration_email' => $configurationEmail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="configuration_email_show", methods={"GET"})
     */
    public function show(ConfigurationEmail $configurationEmail): Response
    {
        return $this->render('configuration_email/show.html.twig', [
            'configuration_email' => $configurationEmail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="configuration_email_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ConfigurationEmail $configurationEmail): Response
    {
        $form = $this->createForm(ConfigurationEmailType::class, $configurationEmail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('configuration_email_index');
        }

        return $this->render('configuration_email/edit.html.twig', [
            'configuration_email' => $configurationEmail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="configuration_email_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ConfigurationEmail $configurationEmail): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$configurationEmail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($configurationEmail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('configuration_email_index');
    }
}
