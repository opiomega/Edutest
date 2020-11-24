<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ClassesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     *  
     * @Route("/", name="category_index", methods={"GET","POST"})
     *
     */
    public function index(CategoryRepository $categoryRepository ,ClassesRepository $classesRepository ,Request $request): Response
    {
      
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
           /* $a=$form->get('photoFile')->getData();
            die($a);*/
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash("success", "New category created ");

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findAll(),
            'form' => $form->createView(),
            'classes' => $classesRepository->findAll(),
        ]);
        
    }

    /**
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash("success", "New category created ");

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->persist($category);
            $this->getDoctrine()->getManager()->flush();
       
            $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute('category_index');
            
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash("delete", "Category deleted ");
        }

        return $this->redirectToRoute('category_index');
    }
 /**
     * @Route("/edit/{id}", name="cat_edit", methods={"GET","POST"})
     */
    public function modif(Request $request, ClassesRepository $classesRepository, Category $category)
    {
        $name=$request->get('name');
     
        $description=$request->get('description');
        $ph=$category->getPhoto();
        dump($ph);
        $photo=$request->get('photo');
        $category->setName($name);
     
        
        if ($photo) {
            $originalFilename = pathinfo($photo, PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            
            $newFilename = $originalFilename.'-'.uniqid().'.'.$photo;

            // Move the file to the directory where brochures are stored
            try {
                $photo->move(
                    $this->getParameter('category_photos'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $category->setPhoto($newFilename);
        }
      
        //$category->addClass($classes);
        $category->setDescription($description);
        $this->getDoctrine()->getManager()->persist($category);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("update", "Informations updated ");
        return $this->redirectToRoute('category_index');

       
    }

    private function countingTeacher($category_id) {
        $teacherQuery = $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
        ->select('count(distinct(t.id))')
        ->from('App:Teacher', 't')
        ->join('App:Module','m')
        ->where('t.id = m.teacher AND m.category = :category_id')
        ->setParameter('category_id', $category_id)
        ->getQuery()
        ->getResult();
;

        return $teacherQuery[0][1];
    }
}
