<?php

namespace App\Controller;

use App\Entity\ClubMessages;
use App\Form\ClubMessagesType;
use App\Repository\ClubMessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Club;

/**
 * @Route("/club/messages")
 */
class ClubMessagesController extends AbstractController
{
    /**
     * @Route("/index/{id}", name="club_messages_index", methods={"GET"})
     */
    public function index(Club $club,ClubMessagesRepository $clubMessagesRepository): Response
    {
        $clubid=$club->getId();
        $clubmessages=$clubMessagesRepository->findBy(array('club'=>$clubid), array('id' => 'desc'));
        return $this->render('club/messages.html.twig', [
            'club_message' => $clubmessages,
            'club' => $club,
        ]);
    }

    /**
     * @Route("/new/{id}", name="club_messages_new", methods={"GET","POST"})
     */
    public function new(Request $request,Club $club): Response
    {
        $clubMessage = new ClubMessages();
        $clubMessage->setClub($club);
        //$thisStudent = $this->getUser()->getStudent();
        $clubMessage->setUser($this->getUser());

        
            $content=$request->get('description');
            

            $clubMessage->setUser($this->getUser());
            $clubMessage->setContent($content);
            $clubMessage->setClub($club);
            $clubMessage->setDateTime(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($clubMessage);
            $entityManager->flush();

            return $this->redirectToRoute('club_messages_index',['id'=> $clubMessage->getClub()->getId()]);
       


     /*   $form = $this->createForm(ClubMessagesType::class, $clubMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($clubMessage);
            $entityManager->flush();

            return $this->redirectToRoute('club_messages_index',['id'=> $clubMessage->getClub()->getId()]);
        }*/

        return $this->redirectToRoute('club_messages_index',['id'=> $clubMessage->getClub()->getId()]);
    }

    /**
     * @Route("/{id}", name="club_messages_show", methods={"GET"})
     */
    public function show(ClubMessages $clubMessage): Response
    {
        return $this->render('club_messages/show.html.twig', [
            'club_message' => $clubMessage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="club_messages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ClubMessages $clubMessage): Response
    {
        $form = $this->createForm(ClubMessagesType::class, $clubMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('club_messages_index',['id'=> $clubMessage->getClub()->getId()]);
        }

        return $this->render('club_messages/edit.html.twig', [
            'club_message' => $clubMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="club_messages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ClubMessages $clubMessage): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$clubMessage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($clubMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('club_messages_index',['id'=> $clubMessage->getClub()->getId()]);
    }
}
