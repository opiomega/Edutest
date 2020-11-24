<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Events;
use App\Entity\Clubphoto;
use App\Form\ClubType;
use App\Form\ClubphotoType;
use App\Form\AboutType;
use App\Form\EventsType;
use App\Repository\ClubRepository;
use App\Repository\ClubMessagesRepository;
use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/clubs")
 */
class ClubController extends AbstractController
{
    /**
     * @Route("/", name="club_index", methods={"GET"})
     */
    public function index(ClubRepository $clubRepository): Response
    {
        $usr= $this->getUser()->getStudent();
      
        return $this->render('club/index.html.twig', [
            'clubs' => $clubRepository->findAll(),
            'student'=>$usr,
        ]);
    }
    

    /**
     * @Route("/new", name="club_new", methods={"GET","POST"})
     */
    public function newAction(Request $request): Response
    {
        $club = new Club();
        if ($this->getUser()->getStatus() == "Teacher"){
            $thisTeacher = $this->getUser()->getTeacher();
            $club->setHead($thisTeacher[0]);
        }
        $form = $this->createForm(ClubType::class, $club,['status'=>$this->getUser()->getStatus()]);
        $form->handleRequest($request);
        
      
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($club);
            $entityManager->flush();

            return $this->redirectToRoute('club_index');
        }

        return $this->render('club/new.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="club_show", methods={"GET"})
     */
    public function show(Club $club): Response
    {
        return $this->render('club/show.html.twig', [
            'club' => $club,
        ]);
    }
    /**
     * @Route("/student/{id}", name="studentclub_show", methods={"GET"})
     */
    public function studentshow(Club $club,EventsRepository $eventsRepository): Response
    {
        $clubid=$club->getId();
        $event=$eventsRepository->findOneBy( array('club'=>$clubid),
        array('id' => 'DESC'));
        return $this->render('club/studentclub.html.twig', [
            'club' => $club,
            'event'=>$event
        ]);
    }
     /**
     * @Route("/home/{id}", name="homeclub_show", methods={"GET"})
     */
    public function home(Club $club,EventsRepository $eventsRepository,ClubMessagesRepository $clubMessagesRepository): Response
    {
        $clubid=$club->getId();
        $event=$eventsRepository->findOneBy( array('club'=>$clubid),
        array('id' => 'DESC'));
        $clubmessages=$clubMessagesRepository->findOneBy(array('club'=>$clubid), array('id' => 'desc'));
        return $this->render('club/home.html.twig', [
            'club' => $club,
            'event'=>$event,
            'club_message' => $clubmessages,
        ]);
    }
    /**
     * @Route("/student/about/{id}", name="about_index", methods={"GET"})
     */
    public function about(Club $club): Response
    {
        return $this->render('club/about.html.twig', [
            'club' => $club,
        ]);
    }
      /**
     * @Route("/student/gallery/{id}", name="gallery_index", methods={"GET"})
     */
    public function gal(Club $club): Response
    {
        return $this->render('club/gallery.html.twig', [
            'club' => $club,
        ]);
    }
    /**
     * @Route("/information/edi/{id}", name="information_index", methods={"GET","POST"})
     */
    public function information(Request $request,Club $club): Response
    {
        $clubid=$club->getId();
        $edit = [true];
        $about=[true];
        $thisStudent = $this->getUser()->getStudent()!==null?$this->getUser()->getStudent():null;
        if ($this->getUser()->getStatus() == "Student" /*|| ( $this->getUser()->getStatus() == "headOfStudents" && isset($thisStudent[0]) && $thisStudent != $club->getLeader() )*/) {
            $this->redirectToRoute("information_index",array('id'=>$clubid));
        }
        $form = $this->createForm(ClubType::class, $club, ["edit"=>$edit,'status'=>$this->getUser()->getStatus()]);
        $form->handleRequest($request);
        $form1 = $this->createForm(AboutType::class, $club, ["edit"=>$edit,"about"=>$about,'status'=>$this->getUser()->getStatus()]);
        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {
            $this->getDoctrine()->getManager()->flush();
           
            return $this->redirectToRoute('information_index',array('id'=>$clubid));
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('information_index',array('id'=>$clubid));
        }
        return $this->render('club/information.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
            'form1' => $form1->createView(),
        ]);
    }
    /**
     * @Route("/events/{id}", name="event_index", methods={"GET","POST"})
     */
    public function eventlist(Club $club): Response
    {
        return $this->render('club/eventlist.html.twig', [
            'club' => $club,
        ]);
    }
    /**
     * @Route("/events/detail/{id}/{event}", name="event_detail", methods={"GET","POST"})
     */
    public function eventdetail(Club $club,$event,EventsRepository $eventsRepository): Response
    {
        $event=$eventsRepository->findOneBy(['id'=>$event]);

      
        return $this->render('club/eventdetail.html.twig', [
            'club' => $club,
            'event'=>$event
        ]);
    }

    /**
     * @Route("/{id}/edit", name="club_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Club $club): Response
    {
        $edit = [true];
        $thisStudent = $this->getUser()->getStudent()!==null?$this->getUser()->getStudent():null;
        if ($this->getUser()->getStatus() == "Student" /*|| ( $this->getUser()->getStatus() == "headOfStudents" && isset($thisStudent[0]) && $thisStudent != $club->getLeader() )*/) {
            $this->redirectToRoute("club_index");
        }
        $form = $this->createForm(ClubType::class, $club, ["edit"=>$edit,'status'=>$this->getUser()->getStatus()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('club_index');
        }

        return $this->render('club/edit.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="club_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Club $club): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$club->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($club);
            $entityManager->flush();
        }

        return $this->redirectToRoute('club_index');
    }

    /**
     * @Route("/event/new/{id}", name="clubevents_new", methods={"GET","POST"})
     */
    public function new(Club $club, Request $request,$id): Response
    {
        $event = new Events();
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);
        $clubid=$club->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $event->setClub($club);
            $entityManager->persist($event);
            
            $entityManager->flush();

            return $this->redirectToRoute('event_index',array('id'=>$id));
        }

        return $this->render('club/newevent.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'club'=>$club
        ]);
    }

    /**
     * @Route("/event/{id}/edit/{event}", name="clubevents_edit", methods={"GET","POST"})
     */
    public function editevent(Request $request, Events $event,Club $club,$id): Response
    {
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_index', array('id' => $id));
        }

        return $this->render('club/eventedit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'club'=>$club
        ]);
    }
    /**
     * @Route("/contact/{id}", name="contact_index", methods={"GET","POST"})
     */
    public function contact(Club $club): Response
    {
        return $this->render('club/contact.html.twig', [
            'club' => $club,
        ]);
    }

    /**
     * @Route("/photo/{id}", name="photo_new", methods={"GET","POST"})
     */
    public function photo(Request $request,Club $club): Response
    {
        $photo= new Clubphoto();
        $form = $this->createForm(ClubphotoType::class,$photo);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $photo->setClub($club);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($photo);
            $entityManager->flush();
            return $this->redirectToRoute('photo_new',["id"=>$club->getId()]);
        }
        return $this->render('club/addpic.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
        ]);
        
    }
}
