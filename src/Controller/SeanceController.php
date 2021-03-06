<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use App\Entity\Seance;
use App\Repository\DaysRepository;
use App\Repository\EducationGroupRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/seance")
 */
class SeanceController extends Controller
{
    /**
     * Lists all Seance entities.
     *@Route("/", name="seance")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Seance')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($seances, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('seance/index.html.twig', array(
            'seances' => $seances,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('App\Form\SeanceFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('SeanceControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('SeanceControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('SeanceControllerFilter')) {
                $filterData = $session->get('SeanceControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('App\Form\SeanceFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }


    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        $sortCol = $queryBuilder->getRootAlias().'.'.$request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show' , 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }
        
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me, $request)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('seance', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
        ));

        return array($entities, $pagerHtml);
    }
    
    
    
    /*
     * Calculates the total of records string
     * 
     */
    protected function getTotalOfRecordsString($queryBuilder, $request) {
        $totalOfRecords = $queryBuilder->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return "Showing $startRecord - $endRecord of $totalOfRecords Records.";
    }
    
    

    /**
     * Displays a form to create a new Seance entity.
     *@Route("/new", name="seance_new")
     */
    public function newAction(Request $request,EducationGroupRepository $educationGroupRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $seance = new Seance();
        $form   = $this->createForm('App\Form\SeanceType', $seance);
        $form->handleRequest($request);
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $date=$request->get('date');
        $datee=new \DateTime($date);
        $dateend=$request->get('dateend');
        $edu=$form->get('educationgroup')->getData();
        //dump($edu);
        $group=$educationGroupRepository->findOneBy(['id'=>$edu]);
        $teacher=$group->getTeacher();
            $seance->setTeacher($teacher);
           $seance->setDateend($dateend);  
            $seance->setDate($date);
           
            $em->persist($seance);
            $em->flush();
            
            $editLink = $this->generateUrl('seance_edit', array('id' => $seance->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New session created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'seance' : 'seance_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('seance/new.html.twig', array(
            'seance' => $seance,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Seance entity.
     *@Route("/{id}/show", name="seance_show")
     */
    public function showAction(Seance $seance)
    {
        $deleteForm = $this->createDeleteForm($seance);
        return $this->render('seance/show.html.twig', array(
            'seance' => $seance,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Seance entity.
     *@Route("/{id}/edit", name="seance_edit")
     */
    public function editAction(Request $request, Seance $seance)
    {
        $deleteForm = $this->createDeleteForm($seance);
        $editForm = $this->createForm('App\Form\SeanceType', $seance);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $date=$request->get('date');
            $dateend=$request->get('dateend');
            //$datee=new \DateTime($date);
            //$seance->setDay($day);
            $seance->setDate($date);
            $seance->setDateend($dateend);
            $em = $this->getDoctrine()->getManager();
            $em->persist($seance);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('seance_edit', array('id' => $seance->getId()));
        }
        return $this->render('seance/edit.html.twig', array(
            'seance' => $seance,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Seance entity.
     *@Route("/{id}/delete", name="seance_delete")
     */
    public function deleteAction($id)
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        $em = $this->getDoctrine()->getManager();
        $seance=$em->getRepository('App:Seance')->find($id);



        if ($seance!=null) {
            $em->remove($seance);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Seance was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Seance');
        }

        return $this->redirectToRoute('seance');
    }
    
    /**
     * Creates a form to delete a Seance entity.
     *
     * @param Seance $seance The Seance entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Seance $seance)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('seance_delete', array('id' => $seance->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Seance by id
     *
     */
    public function deleteByIdAction(Seance $seance){
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($seance);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Seance was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Seance');
        }

        return $this->redirect($this->generateUrl('seance'));

    }
    

    /**
    * Bulk Action
    *@Route("/bulk-action", name="seance_bulk_action")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Seance');

                foreach ($ids as $id) {
                    $seance = $repository->find($id);
                    $em->remove($seance);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'seances was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the seances ');
            }
        }

        return $this->redirect($this->generateUrl('seance'));
    }


    public function testAction()
    {
        echo "tesrt";
        return new Response("test");
    }
    

}
