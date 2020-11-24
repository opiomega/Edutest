<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\TypeCourse;
use App\Entity\Category;
use App\Entity\Teacher;
use App\Entity\EducationGroup;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use App\Repository\CategoryRepository;
use App\Repository\TypeCourseRepository;
use App\Repository\ClassesRepository;
use App\Repository\ModuleDocumentRepository;
use App\Repository\EducationGroupRepository;
use App\Repository\WeeksRepository;
use App\Repository\TopicRepository;
use App\Repository\TestRepository;
use App\Repository\TestScoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\AbstractType;


use App\Entity\Student;
use App\Repository\TeacherRepository;
use App\Repository\StudentRepository;

use FOS\CKEditorBundle\Form\Type\CKEditorType;

/**
 * @Route("/module")
 */
class ModuleController extends AbstractController
{
    /**
     * @Route("/student/{idCategory}/{idClass}", name="module_student",methods={"GET"})
     */
    public function studindex(ModuleRepository $moduleRepository,StudentRepository $studentRepository,CategoryRepository $categoryRepository,TypeCourseRepository $typeCourseRepository,$idCategory,$idClass){

        $teacher = $this->getUser()->getTeacher();
        $student = $this->getUser()->getStudent();
        $category = $categoryRepository->find($idCategory);
        if (isset($student[0]) ){
            /*if ($student[0]->getEducationGroups())
                $modules = $moduleRepository->findBy(["category"=>$idCategory,"classe"=>$idClass,"educationGroup"=>$student[0]->getEducationGroups()->getId()]);
            else
                $modules = [];*/
            $educationGroups = $student[0]->getEducationGroups();
            $modules = [];
            foreach ($educationGroups as $educationGroup) {
                //$modules = array_merge($modules,$educationGroup->getModule()->getValues());
                /*foreach ($educationGroup->getModule()->getValues() as $module){
                    if ($module->getCategory()!=null && $module->getClasse()!=null && $module->getCategory()->getId()==$idCategory && $module->getClasse()->getId()==$idClass)
                        $modules[]=$module;
                }*/
                if ($educationGroup->getClasse()!=null && $educationGroup->getClasse()->getId() == $idClass)
                    $modules = array_merge($modules, $educationGroup->getClasse()->getModules()->getValues());
            }
        }
        else
            $modules = $moduleRepository->findBy(["category"=>$idCategory]);

        return $this->render('module/module.html.twig',[
            'catagories' => $categoryRepository->findAll(),
            'typeCourses' => $typeCourseRepository->findAll(),
            'modules' => $modules,
            'idCategory'=>$idCategory,
            'idClass'=>$idClass,
            'classes' => $category->getClasses(),
        ]);

    }
  


    /**
     * @Route("/", name="module_selectCategory", methods={"GET"})
     */
    public function selectCategory(StudentRepository $studentRepository,CategoryRepository $categoryRepository,TypeCourseRepository $typeCourseRepository): Response
    {
        $categoriesUnlocked = []; 
        if ($this->getUser()->getStatus()=="Student"){
            if (!$this->getUser()->getStudent()->first()->getEducationGroups()->isEmpty()){
                $ides = $this->getUser()->getStudent()->first()->getEducationGroups()->map(function($obj){return $obj->getId();})->getValues();
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb->select('c');
                $qb->from('App:Classes', 'c');
                $qb->join('App:EducationGroup','e','WITH','e.classe = c.id');
                $qb->where($qb->expr()->in('e.id',$ides));
                $classesUnlocked = $qb->getQuery()->getResult();
            }
            else {
                $classesUnlocked = [];
            }
            foreach ($classesUnlocked as $classe){
                if (!in_array($classe->getCategory(), $categoriesUnlocked))
                        $categoriesUnlocked[]=$classe->getCategory();
            }
        }
        
        //var_dump($categoriesUnlocked);
        /*if ($this->getUser()->getStatus()=="Student"){
            $categoriesUnlocked = $this->getDoctrine()->getManager()->getRepository('App:Classes')->findBy('');
        }*/
        return $this->render('module/selectCategory.html.twig', [
            'catagories' => $categoryRepository->findAll(),
            'typeCourses' => $typeCourseRepository->findAll(),
            'students' => $studentRepository->findAll(),
            'categoriesUnlocked' => $categoriesUnlocked,
        ]);
    }
    
    /**
     * @Route("type/{id}", name="module_selectType", methods={"GET"})
     */
    public function selectType(TypeCourseRepository $typeCourseRepository,$id): Response
    {
        return $this->render('module/selectType.html.twig', [
            'typeCourses' => $typeCourseRepository->findAll(),
            'categoryId' => $id,
        ]);
    }
    
    /**
     * @Route("/selectModuleClasse/{id}", name="module_selectClasse", methods={"GET"})
     */
    public function selectModuleClasse(TypeCourseRepository $typeCourseRepository , CategoryRepository $categoryRepository,Category $category): Response
    {
        $categoriesUnlocked = []; 
        if ($this->getUser()->getStatus()=="Student"){
            $ides = $this->getUser()->getStudent()->first()->getEducationGroups()->map(function($obj){return $obj->getId();})->getValues();
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->select('c');
            $qb->from('App:Classes', 'c');
            $qb->join('App:EducationGroup','e','WITH','e.classe = c.id');
            $qb->where($qb->expr()->in('e.id',$ides));
            $classesUnlocked = $qb->getQuery()->getResult();
            foreach ($classesUnlocked as $classe){
                if (!in_array($classe->getCategory(), $categoriesUnlocked))
                        $categoriesUnlocked[]=$classe->getCategory();
            }
            //$classesUnlocked = $this->getDoctrine()->getManager()->getRepository('App:Classes')->findBy(['educationGroup'=>$this->getUser()->getStudent()->first()->getEducationGroups()]);
        }
        return $this->render('module/selectModuleClasse.html.twig', [
            'typeCourses' => $typeCourseRepository->findAll(),
            'classes' => $category->getClasses(),
            'classesUnlocked'=>isset($classesUnlocked)?$classesUnlocked:null,
            'catagories' => $categoryRepository->findAll(),
            'categoriesUnlocked'=> $categoriesUnlocked,
        ]);
    }

    /**
     * @Route("/index/{idType}/{idCategory}/{idClass}", name="module_index", methods={"GET"})
     */
    public function index($idType,ModuleRepository $moduleRepository,TypeCourseRepository $typeCourseRepository, CategoryRepository $categoryReposetory, ClassesRepository $classesRepository/*,$idType*/,$idCategory,$idClass): Response
    {
        $teacher = $this->getUser()->getTeacher();
        $student = $this->getUser()->getStudent();
        $modules = [];
        if ($this->getUser()->getStatus()=="Teacher"){
            //$modules = $moduleRepository->findBy(["TypeCourse"=>$idType,"category"=>$idCategory,"classe"=>$idClass,"teacher"=>$teacher[0]->getId()]);
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                        $qb->select('m');
                        $qb->from(Module::class,'m');
                        $qb->join(Teacher::class,'t');
                        $qb->join(EducationGroup::class,'e');
                        $qb->where("m.classe=e.classe and t.id=e.teacher and t.id=:teacher and m.classe=:classe");
                        $qb->setParameter('teacher',$teacher[0]);
                        $qb->setParameter('classe',$idClass);
                        $modules = $qb->getQuery()->getResult();
        }
        
        else /*if ($this->getUser()->getStatus() == "Admin")*/
            $modules = $moduleRepository->findBy([/*"TypeCourse"=>$idType,*/"category"=>$idCategory,"classe"=>$idClass]);
        
        if($this->getUser()->getStatus()=="Student"){
            $found = false;
            foreach ($student[0]->getEducationGroups() as $educationGroup){
                if ($educationGroup->getClasse()!==null && $educationGroup->getClasse()->getId()==$idClass){
                    $found = true;
                }
            }
            if (!$found){
                throw new \Exception("Access denied");
            }
        }
        
        $typeCourse = $typeCourseRepository->find($idType);
        $category = $categoryReposetory->find($idCategory);
        $class = $classesRepository->find($idClass);


        $categoriesUnlocked = []; 
        if ($this->getUser()->getStatus()=="Student"){
            if (!$this->getUser()->getStudent()->first()->getEducationGroups()->isEmpty()){
                $ides = $this->getUser()->getStudent()->first()->getEducationGroups()->map(function($obj){return $obj->getId();})->getValues();
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb->select('c');
                $qb->from('App:Classes', 'c');
                $qb->join('App:EducationGroup','e','WITH','e.classe = c.id');
                $qb->where($qb->expr()->in('e.id',$ides));
                $classesUnlocked = $qb->getQuery()->getResult();
            }
            else {
                $classesUnlocked = [];
            }
            foreach ($classesUnlocked as $classe){
                if (!in_array($classe->getCategory(), $categoriesUnlocked))
                        $categoriesUnlocked[]=$classe->getCategory();
            }
        }
        $form=$this->createFormBuilder()
            ->add('content',CKEditorType::class,[
                'config'=>[
                    'uiColor'=>'#e2e2e2',
                    'toolbar'=>'full'
                ],
                'filebrowsers' => array(
                    'VideoUpload',
                    'VideoBrowse',
                )
            ])
            ->getForm();
        return $this->render('module/index.html.twig', [
            'catagories' => $categoryReposetory->findAll(),
            'modules' => $modules,
            'category' => $category,
            'class'=> $class,
            'categoriesUnlocked'=> $categoriesUnlocked,
            'classes'=>$classesRepository->findAll(),
            'form'=>$form->createView(),
            'typeCourse'=>$typeCourse
        ]);
    }
    
    /**
     * @Route("/new/{idType}/{idCategory}/{idClass}", name="module_new", methods={"GET","POST"})
     */
    public function newAction(TypeCourseRepository $typeCourseRepository, CategoryRepository $categoryReposetory, ClassesRepository $classesRepository ,Request $request,$idType=null,$idCategory=null,$idClass=null): Response
    {
        //TypeCourseRepository $typeCourseReposetory
        $selectOptions = ['typeCourse','category'];
        //$categoriesList = $typeCourseReposetory->findAll();
        /*if ($request->get('typeCourseSelected')) {
            $typeCourseSelected = $this->getDoctrine()
        ->getRepository(TypeCourse::class)
        ->find($request->get('typeCourseSelected'));
            $selectOptions = $typeCourseSelected->getFieldsAvailable() ;
        }*/
        if ($this->getUser()->getStatus()!="Admin"){
            $this->addFlash("danger", "This user is not admin");
            return $this->redirectToRoute('module_selectCategory');
        }
        $module = new Module();
        $teacher = $this->getUser()->getTeacher();
        $entityManager = $this->getDoctrine()->getManager();
        if (isset($teacher[0]))
            $module->setTeacher($teacher[0]);
        if ($idType!=null && $idCategory!=null && $idClass!=null) {
            $typeCourse = $typeCourseRepository->find($idType);
            $category = $categoryReposetory->find($idCategory);
            $classe = $classesRepository->find($idClass);
            $module->setcategory($category);
            $module->setTypeCourse($typeCourse);
            $module->setClasse($classe);
            $entityManager->persist($module);
            $entityManager->flush();
            return $this->redirectToRoute('module_complete',['id'=>$module->getId()]);
        }
        else {
            $selectOptions = ['typeCourse','category'];
        }
    $form = $this->createForm(ModuleType::class, $module,['selectOptions'=>$selectOptions]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data->getCategory()!=$data->getClasse()->getCategory()) {
                $this->addFlash('danger','This class is not the category');
                return $this->render('module/new.html.twig', [
                    'module' => $module,
                    'form' => $form->createView(),
                ]);
            }
            $entityManager->persist($module);
            $entityManager->flush();
            $this->addFlash("success", "New Module created ");

            return $this->redirectToRoute('module_complete',['id'=>$module->getId()]);
        }


        return $this->render('module/new.html.twig', [
            'module' => $module,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{id}/complete", name="module_complete", methods={"GET","POST"})
     */
    public function complete(Request $request, Module $module): Response
        
    {
       
        
        if ($this->getUser()->getStatus()!="Admin"){
            $this->addFlash("danger", "This user is not admin");
            return $this->redirectToRoute('module_selectCategory');
        }
        /*if( $this->getUser()->getStatus() == "Teacher" )
        {
            $test= $this->getUser()->getTeacher();
            $test1 = $test[0]->getEducationGroups();
            if (!$test1[0]) {
                $this->addFlash("danger","you must be affected to a class to be able to create a courses");
                return $this->redirectToRoute('module_index',["idType"=>$module->getTypeCourse()->getId(),"idCategory"=>$module->getCategory()->getId()]);
            }
       //return new RedirectResponse($referer);
        }*/
        $selectOptions = $module->getTypeCourse()->getFieldsAvailable() ;
        $form = $this->createForm(ModuleType::class, $module,['selectOptions'=>$selectOptions]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if( $this->getUser()->getStatus() == "Teacher" )
            {
                $test= $this->getUser()->getTeacher();
                $test1 = $test[0]->getEducationGroups();
                $module->setEducationGroup($test1[0]);
            }
            $this->getDoctrine()->getManager()->flush();
            if ($module->getTypeCourse()->getName() === "Course" && !$request->get('saveWAD'))
                return $this->redirectToRoute('module_document_new',["id"=>$module->getId()]);
            return $this->redirectToRoute('module_index',["idType"=>$module->getTypeCourse()->getId(),"idCategory"=>$module->getCategory()->getId(),"idClass"=>$module->getClasse()->getId()]);
        }

        return $this->render('module/complete.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("show/{idClass}/{idWeek}/{id}", name="module_show", methods={"GET"})
     */
    public function show($idWeek,Module $module,ModuleDocumentRepository $moduleDocumentRepository,ClassesRepository $classesRepository,CategoryRepository $categoryRepository,TypeCourseRepository $typeCourseRepository,$idClass,WeeksRepository $weeksRepository,EducationGroupRepository $educationGroupRepository,TopicRepository $topicRepository): Response
    {
        
        $moduleId=$module->getId();
        $class=$classesRepository->findoneby(['id'=>$idClass]);
        $classname= $class->getName();
        $category=$class->getCategory();
        $catname=$category->getName();
        $group=$educationGroupRepository->findoneby(['classe'=>$idClass]);
        $topics=$topicRepository->findby(['educationGroup'=>$group]);
        //$weeks = $class->getWeeks()->getTitle();
        //die($weeks);
        $week=$weeksRepository->findOneBy(['id'=>$idWeek]);
        $weeks=$class->getSemaine();
       // $weeks=$weeksRepository->findby(['classes'=>$idClass]);
        $firstweek=$weeks[0]->getTitle();
        $topic=$module->getTopic();
        return $this->render('module/show.html.twig', [
            'module_documents' => $moduleDocumentRepository->findBy(["module"=>$moduleId]),
            'module' => $module,
            'weeks'=>$weeks,
            'topics'=>$topics,
           'topic'=>$topic,
            'classname'=>$classname,
            'catname'=>$catname,
            'firstweek'=>$firstweek,
            'class'=>$class,
            'week'=>$week
        ]);
    }

    /**
     * @Route("/{id}/edit", name="module_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Module $module): Response
    { 
        $edit = [true];
        $selectOptions = $module->getTypeCourse()->getFieldsAvailable() ;
        $form = $this->createForm(ModuleType::class, $module,['selectOptions'=>$selectOptions, "edit"=>$edit]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
            return $this->redirectToRoute('module_index',["idType"=>$module->getTypeCourse()->getId(),"idCategory"=>$module->getCategory()->getId(),"idClass"=>$module->getClasse()->getId()]);
        }
        //dump($selectOptions);
        return $this->render('module/edit.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
            'selectOptions'=>$selectOptions,
            
        ]);
    }

    /**
     * @Route("/delete/{id}", name="module_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Module $module): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->isCsrfTokenValid('delete'.$module->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($module->getModuleDocuments() as $moduleDocument) {
                $entityManager->remove($moduleDocument);
            }
            $entityManager->remove($module);
            $entityManager->flush();
            $this->addFlash("delete", "Course deleted ");
        }

        return $this->redirectToRoute('module_index',["idType"=>$module->getTypeCourse()->getId(),"idCategory"=>$module->getCategory()->getId(),'idClass'=>$module->getClasse()->getId()]);
    }



    /**
     * @Route("/videolist", name="list", methods={"GET"})
     */
    public function listvid(ModuleRepository $moduleRepository,TypeCourseRepository $typeCourseRepository, CategoryRepository $categoryReposetory): Response

    {
        $teacher = $this->getUser()->getTeacher();
        $student = $this->getUser()->getStudent();
        $modules = [];

        

        if (isset($teacher[0]) )
            $educationGroups = $teacher[0]->getEducationGroups();
        else if(isset($student[0]) )
            $educationGroups = $student[0]->getEducationGroups();
        $modules = [];
        foreach ($educationGroups as $educationGroup) {
            $modules = array_merge($modules,$educationGroup->getModule()->getValues());
        }
        if ($this->getUser()->getStatus() == "Admin")
            $modules = $moduleRepository->findAll();
            $typeCourse = $typeCourseRepository->findAll();
            $category = $categoryReposetory->findAll();
        return $this->render('module/listvideo.html.twig', [
            'modules' => $modules,
            'typeCourse' => $typeCourse,
            'categories' => $category,
        ]);
    }

    /**
     * @Route("/form/dashboard/index/student/course/{id}", name="module_test_index_student", methods={"GET"})
     */
    public function indexStudentCoursesByEducationGroup(EducationGroup $educationGroup): Response
    {
        
        return $this->render('module/indexStudent.html.twig', [
            'educationGroup'=>$educationGroup
        ]);
    }
   /**
     * @Route("/weeks/{idClass}/{idWeek}", name="module_weeks",methods={"GET"})
     */
    public function weeks($idWeek,ModuleRepository $moduleRepository,ClassesRepository $classesRepository,CategoryRepository $categoryRepository,TypeCourseRepository $typeCourseRepository,$idClass,WeeksRepository $weeksRepository,EducationGroupRepository $educationGroupRepository,TopicRepository $topicRepository,TestRepository $testRepository,TestScoreRepository $testScoreRepository)
        {
            $categoriesUnlocked = []; 
        if ($this->getUser()->getStatus()=="Student"){
            if (!$this->getUser()->getStudent()->first()->getEducationGroups()->isEmpty()){
                $ides = $this->getUser()->getStudent()->first()->getEducationGroups()->map(function($obj){return $obj->getId();})->getValues();
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb->select('c');
                $qb->from('App:Classes', 'c');
                $qb->join('App:EducationGroup','e','WITH','e.classe = c.id');
                $qb->where($qb->expr()->in('e.id',$ides));
                $classesUnlocked = $qb->getQuery()->getResult();
            }
            else {
                $classesUnlocked = [];
            }
            foreach ($classesUnlocked as $classe){
                if (!in_array($classe->getCategory(), $categoriesUnlocked))
                        $categoriesUnlocked[]=$classe->getCategory();
            }
        }
        $class=$classesRepository->findoneby(['id'=>$idClass]);
        $classname= $class->getName();
        $category=$class->getCategory();
        
        $catname=$category->getName();
        $group=$educationGroupRepository->findoneby(['classe'=>$idClass]);
        $wee=$weeksRepository->findoneby(['id'=>$idWeek]);
        $topics=$topicRepository->findby(['educationGroup'=>$group,'week'=>$wee]);
        //$weeks = $class->getWeeks()->getTitle();
        //die($weeks);
        $weeks=$class->getSemaine();
        $semaine=[];
        foreach($weeks as $week)
        {
            $semaine[]=$week;
        }
       // dump($semaine);
      //  $weeks=$weeksRepository->findby(['classe'=>$idClass]);
        if ($semaine!= null ){
            $firstweek=$weeks[0];
        }
        $firstweek=null;
        $modules=$moduleRepository->findBy([/*"TypeCourse"=>$idType,*/"classe"=>$idClass]);
            
        $student = $this->getUser()->getStudent();
        
       
        $idCategory=$category->getId();
        if($student[0]->getEducationGroups() !=null){
            $tests = [];
            foreach ($student[0]->getEducationGroups() as $group){
                //$tests = array_merge($tests,$group->getTests()->getValues());
                foreach ($group->getTests()->getValues() as $test){
                    if ($test->getCategory()!=null && $test->getClasse()!=null && $test->getCategory()->getId()==$idCategory && $test->getClasse()->getId()==$idClass)
                        $tests[]=$test;
                }
            }
        }
        $scores=$testScoreRepository->findby(['student'=>$student[0]]);
            return $this->render('module/weeks.html.twig', [
                'catagories' => $categoryRepository->findAll(),
                'typeCourses' => $typeCourseRepository->findAll(),
                'categoriesUnlocked' => $categoriesUnlocked,
                'class'=>$class,
                'weeks'=>$semaine,
                'topics'=>$topics,
                "modules"=>$modules,
                'classname'=>$classname,
                'catname'=>$catname,
                'firstweek'=>$firstweek,
                'semainee'=>$wee,
                'tests'=>$tests,
                'scores'=>$scores
            ]);
        }

    /**
     * Resorts an item using it's doctrine sortable property
     * @param integer $id
     * @param integer $position
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sortAction($id, $position)
    {
        $em = $this->getDoctrine()->getManager();
        $productCategory = $em->getRepository('App:Module')->find($id);
        $productCategory->setPosition($position);
        $em->persist($productCategory);
        $em->flush();

        $request = new Request();

        return $this->index($request);
    }
      
}

