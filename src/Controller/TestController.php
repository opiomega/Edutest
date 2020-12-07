<?php

namespace App\Controller;

use App\Entity\Test;
use App\Form\TestType;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Teacher;
use App\Entity\EducationGroup;
use App\Repository\TestRepository;
use App\Repository\TestScoreRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\ClassesRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\StudentRepository;
use App\Entity\TestScore;
use App\Entity\Profession;
use App\Repository\ContractRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\Reponse;

/**
 * @Route("/test")
 */
class TestController extends Controller
{
    
    /**
     * @Route("/", name="test_selectCategory", methods={"GET"})
     */
    public function selectTestCategory(CategoryRepository $categoryRepository): Response
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
        
        return $this->render('test/selectTestCategory.html.twig', [
            'catagories' => $categoryRepository->findAll(),
            'categoriesUnlocked' => $categoriesUnlocked,
            
        ]);
    }
    
    /**
     * @Route("/selectClasse/{id}", name="test_selectClasse", methods={"GET"})
     */
    public function selectTestClasse(CategoryRepository $categoryRepository,Category $category): Response
    {
        return $this->render('test/selectTestClasse.html.twig', [
            'classes' => $category->getClasses(),
            'catagories' => $categoryRepository->findAll(),
        ]);
    }
    
    /**
     * @Route("/index/{idCategory}/{idClass}", name="test_index", methods={"GET"})
     */
    public function index(TestRepository $testRepository,CategoryRepository $categoryRepository, ClassesRepository $classesRepository,$idCategory,$idClass): Response
    {
        $teacher = $this->getUser()->getTeacher();
        if ($this->getUser()->getStatus() == "Teacher"){
            $eductaionGroups = $teacher[0]->getEducationGroups();
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                        $qb->select('te');
                        $qb->from(Test::class,'te');
                        $qb->join(Teacher::class,'t');
                        $qb->join(EducationGroup::class,'e');
                        $qb->where("te.educationGroup=e.id and t.id=e.teacher and t.id=:teacher and te.classe=:classe");
                        $qb->setParameter('teacher',$teacher[0]);
                        $qb->setParameter('classe',$idClass);
                        $tests = $qb->getQuery()->getResult();
            return $this->render('test/index.html.twig', [
                'tests' => $tests,
                'category' => $categoryRepository->find($idCategory)
            ]);
        }
        if ($this->getUser()->getStatus() == "Student" ){
         
            $student = $this->getUser()->getStudent();
            //if($student[0]->getTeacher() !=null)
            return $this->render('test/index.html.twig', [
                'tests' => $testRepository->findBy(["category"=>$idCategory,"teacher"=>$student[0]->getTeacher()->getId()]),
                'category' => $categoryRepository->find($idCategory),
                'categories' => $categoryRepository->findAll(),
                'classes' => $categoryRepository->find($idCategory)->getClasses(),
                'classeId' => $classesRepository->find($idClass)->getId(),
            ]);
            /*else {
                return $this->render('test/index.html.twig',[
                    'tests'=>[],
                    'category' => $categoryRepository->find($idCategory)
                ]);
                
                
            }*/
        }
        return $this->render('test/index.html.twig', [
            'tests' => $testRepository->findBy(["category"=>$idCategory,"type"=>"normal"]),
            'category' => $categoryRepository->find($idCategory),
            'classe' => $classesRepository->find($idClass),
        ]);
    }

    /**
     * @Route("/new/{idCategory}/{idClasse}", name="test_new", methods={"GET","POST"})
     */
    public function newAction(Request $request,CategoryRepository $categoryRepository, ClassesRepository $classesRepository,$idCategory = null,$idClasse = null): Response
    {
        $test = new Test();
        //$form = $this->createForm(ModuleType::class, $test,['selectOptions'=>$selectOptions])
        $selectOptions = ['category','classe'];
        if ($this->getUser()->getStatus()=="Admin")
            $form = $this->createForm(TestType::class, $test,['selectOptions'=>$selectOptions,'admin'=>[true]]);
        else
            {
                $this->addFlash("danger", "This user is not admin");
                return $this->redirectToRoute('test_selectCategory');
            }
        $form->handleRequest($request);
        $teacher = $this->getUser()->getTeacher();
        if ($this->getUser()->getStatus()!="Admin"){
            $this->addFlash("danger", "This user is not admin");
            return $this->redirectToRoute('test_selectCategory');
        }
        $entityManager = $this->getDoctrine()->getManager();
        
        if ($idCategory != null){
            $category = $categoryRepository->find($idCategory);
            $classe = $classesRepository->find($idClasse);
            $test->setCategory($category);
            $test->setClasse($classe);
            $entityManager->persist($test);
            $entityManager->flush();
            return $this->redirectToRoute('test_complete',["id"=>$test->getId()]);
        }
    
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data->getCategory()!=$data->getClasse()->getCategory()) {
                $this->addFlash('danger','This class is not the category');
                return $this->render('test/new.html.twig', [
                    'test' => $test,
                    'form' => $form->createView(),
                ]);
            }
            $entityManager = $this->getDoctrine()->getManager();
        
            $test->setType('normal');
            $entityManager->persist($test);
            $entityManager->flush();

            return $this->redirectToRoute('test_complete',["id"=>$test->getId()]);
        }

        return $this->render('test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/complete/{id}", name="test_complete", methods={"GET","POST"})
     */
    public function complete(Request $request,Test $test): Response
    {
        //$test = new Test();
        $selectOptions = ['title','content','description','supportPdfFile','listeningFile','educationGroup','deadline'];
        if ($this->getUser()->getStatus()=="Admin"){
            //$selectOptions[]='type';
            $form = $this->createForm(TestType::class, $test,['selectOptions'=>$selectOptions,'admin'=>[true]]);
        }
        else
            {
                $this->addFlash("danger", "This user is not admin");
                return $this->redirectToRoute('test_selectCategory');
            }
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($test);
            $entityManager->flush();
            $this->addFlash("success", "New Test created ");

            return $this->redirectToRoute('question_new',["id"=>$test->getId()]);
        }

        return $this->render('test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/palcement/test/new", name="test_new_placement", methods={"GET","POST"})
     */
    public function newPalcementTestAction(Request $request,CategoryRepository $categoryRepository, ClassesRepository $classesRepository,$idCategory = null,$idClasse = null): Response
    {
        $test = new Test();
        //$form = $this->createForm(ModuleType::class, $test,['selectOptions'=>$selectOptions])
        $selectOptions = ['category'];
        if ($this->getUser()->getStatus()=="Admin")
            $form = $this->createForm(TestType::class, $test,['selectOptions'=>$selectOptions,'admin'=>[true]]);
        else
            {
                $this->addFlash("danger", "This user is not admin");
                return $this->redirectToRoute('test_selectCategory');
            }
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
  
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $testExist = $entityManager->getRepository('App:Test')->findBy(['type'=>'level','category'=>$test->getCategory()]);
            if (isset($testExist[0]))
                return $this->redirectToRoute('test_edit',['id'=>$testExist[0]->getId(),'exist'=>'exist']);
            /*else
                
            $test->setType('level');
            $entityManager->persist($test);
            $entityManager->flush();

            return $this->redirectToRoute('question_new',["id"=>$test->getId()]);*/
            return $this->redirectToRoute('test_complete_placement',['id'=>$data->getCategory()->getId(),'exist'=>'exist']);
        }

        
        return $this->render('test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/palcement/test/complete/{id}", name="test_complete_placement", methods={"GET","POST"})
     */
    public function completePalcementTestAction(Request $request,Category $category): Response
    {
        $test = new Test();
        //$form = $this->createForm(ModuleType::class, $test,['selectOptions'=>$selectOptions])
        $selectOptions = ['title','description','supportPdfFile','listeningFile'];
        if ($this->getUser()->getStatus()=="Admin")
            $form = $this->createForm(TestType::class, $test,['selectOptions'=>$selectOptions,'admin'=>[true]]);
        else
            {
                $this->addFlash("danger", "This user is not admin");
                return $this->redirectToRoute('test_selectCategory');
            }
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
  
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $testExist = $entityManager->getRepository('App:Test')->findBy(['type'=>'level','category'=>$test->getCategory()]);             
            $test->setType('level');
            $test->setCategory($category);
            $entityManager->persist($test);
            $entityManager->flush();

            return $this->redirectToRoute('question_new',["id"=>$test->getId()]);
        }

        return $this->render('test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/{id}", name="test_show", methods={"GET"})
     */
    public function show(Test $test): Response
    {
        return $this->render('test/show.html.twig', [
            'test' => $test,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="test_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Test $test): Response
    {
        $edit = [true];
        if ($request->get('exist')!==null && $request->get('exist'))
            $this->addFlash('danger','This test exist.Do you want to modify it');
        $selectOptions = ['title','description','supportPdfFile','listeningFile','classe'];
        if ($test->getType()=='normal')
            $selectOptions[]='deadline';
        if ($this->getUser()->getStatus()=="Admin")
            $form = $this->createForm(TestType::class, $test,['selectOptions'=>$selectOptions,'admin'=>[true]]);
        else
            {
                $this->addFlash("danger", "This user is not admin");
                return $this->redirectToRoute('test_selectCategory');
            }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data->getCategory()!=$data->getClasse()->getCategory()) {
                $this->addFlash('danger','This class is not the category');
                return $this->render('test/edit.html.twig', [
                    'test' => $test,
                    'form' => $form->createView(),
                ]);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Informations updated ");
            //return $this->redirectToRoute('test_index',["idCategory"=>$test->getCategory()->getId(),"idClass"=>$test->getClasse()->getId()]);
	   return $this->redirectToRoute('testadmin_index');
        }

        return $this->render('test/edit.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/remove/document/{id}", name="remove_document", methods={"GET","POST"})
     */
    public function removeDocument(Request $request, Test $test): Response
    {
        
            $test->setSupportPdf(null);
            $this->getDoctrine()->getManager()->persist($test);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("update", "Document successfully removed ");


        return $this->redirectToRoute('test_edit',['id'=>$test->getId()]);
    }

    /**
     * @Route("/{id}", name="test_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Test $test): Response
    {
        if ($this->getUser()->getEmail()!="s.zarrouk@edu-test.co")
            throw new \Exception("Access denied");
        if ($this->getUser()->getStatus()!="Admin"){
            $this->addFlash("danger", "This user is not admin");
            return $this->redirectToRoute('test_selectCategory');
        }
        if ($this->isCsrfTokenValid('delete'.$test->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($test);
            $entityManager->flush();
            $this->addFlash("delete", "Test deleted ");
        }

        return $this->redirectToRoute('testadmin_index');
    }



     /**
     * @Route("/list/admin", name="testadmin_index", methods={"GET"})
     */
    public function adminindex(TestRepository $testRepository): Response
    {
        return $this->render('test/adminindex.html.twig', [
            'tests' => $testRepository->findBy(['type'=>'normal']),
            
            
        ]);
    }

    /**
     * @Route("/list/student/{idCategory}/{idClass}", name="teststudent_index", methods={"GET"})
     */
    public function studentindex(StudentRepository $studentRepository,TestRepository $testRepository,CategoryRepository $categoryRepository,$idCategory,$idClass,ClassesRepository $classesRepository,TestScoreRepository $testScoreRepository ): Response
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
        $teacher = $this->getUser()->getTeacher();

        $student = $this->getUser()->getStudent();
        
        $category = $categoryRepository->find($idCategory);
        $classe=$classesRepository->find($idClass);
        $catname=$category->getName();
        $classname=$classe->getName();
        if($student[0]->getEducationGroups() !=null){
            $tests = [];
            foreach ($student[0]->getEducationGroups() as $group){
                //$tests = array_merge($tests,$group->getTests()->getValues());
                foreach ($group->getTests()->getValues() as $test){
                    if ($test->getCategory()!=null && $test->getClasse()!=null && $test->getCategory()->getId()==$idCategory && $test->getClasse()->getId()==$idClass)
                        $tests[]=$test;
                }
            }
         $scores=$testScoreRepository->findby(['student'=>$student[0]]);
        
        return $this->render('test/studenttest.html.twig', [
            'tests' => $tests,
            'categories' => $categoryRepository->findAll(),
            'classes' => $category->getClasses(),
            'students' => $studentRepository->findAll(),
            'idCategory'=>$idCategory,
            'idClass' => $idClass,
            'categoriesUnlocked' => $categoriesUnlocked,
            'catname'=>$catname,
            'classname'=>$classname,
            'scores'=>$scores
            
        ]);
        }
        else
        return $this->render('test/studenttest.html.twig', [
            'tests' => [],
            'categories' => $categoryRepository->findAll(),
            'classes' => $category->getClasses(),
            'students' => $studentRepository->findAll(),
            'idCategory'=>$idCategory,
            'idClass' => $idClass,
        ]);
        
    }


    /**
     * @Route("/pass/exam/{id}", name="pass_exam")
     */
    public function passExam(Test $test)
    {
        $today = new \DateTime() ;
        if ($test->getDeadline()>=$today){
        $student = $this->getUser()->getStudent();
        if(array_uintersect($test->getTestScores()->getValues(),$student[0]->getTestScore()->getValues(),function ($e1, $e2) { 
            if($e1->getId() == $e2->getId() ) {
                return 0;
            } else {
                return 1;
            }
            
        })){
            $this->addFlash("danger", "Becaful you have already passed this exam");
            return $this->redirectToRoute('teststudent_index',["idCategory"=>$test->getCategory()->getId(),"idClass"=>$test->getClasse()->getId()]);
        }

        
        return $this->render('test/pass_exam.html.twig', [
            'controller_name' => 'PassExamController',
            'test' => $test,
            
        ]);
        }
        $this->addFlash("danger", "You cannot take this exam, deadline exceeded");
        return $this->redirectToRoute('teststudent_index',["idCategory"=>$test->getCategory()->getId(),"idClass"=>$test->getClasse()->getId()]);
    }
    
    /**
     * @Route("/exam/result", name="exam_result")
     */
    public function addScoreToTest(Request $request, TestRepository $testReposetory){
        $testScore = new TestScore();
        $test = $testReposetory->find($request->get('test'));
        $testScore->setValue($request->get('score'));
        $student = $this->getUser()->getStudent()!==null?$this->getUser()->getStudent():die("This user is not student");
        $testScore->setStudent($student[0]);
        $testScore->setTest($test);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($testScore);
        $entityManager->flush();
        $response['score']= floatval($request->get('score'));
        return new Response(json_encode($response));
    }
    
     /**
     * @Route("/pass/level/test/{username}", name="pass_level_test")
     */
    public function passLevelTest(Request $request,$username, TestRepository $testRepository,ContractRepository $contractRepository)
    {
        $key=$request->get('key');
        $student = $this->getDoctrine()->getManager()->createQueryBuilder()->select('s')->from(Student::class, 's')->join(User::class,'u')->where('s.user = u.id and u.email = :username')->setParameter('username',$username)->getQuery()->getResult();
        if ($key!=md5($student[0]->getUser()->getEmail()))
            return $this->redirectToRoute("app_login");
        //die(var_dump($student));
        $levelTest = $testRepository->findBy(['type'=>'level','category'=>$student[0]->getLevelTestType()]);
        /*if (!isset($levelTest[0])){
            $this->addFlash('danger', 'Sorry there is no level test affected to you yet. You can contact our team!');
           return $this->redirectToRoute('app_logout');
        }*/
        
        $test = $levelTest[0];
        if ($test->getType()!="level"){
        if(array_uintersect($test->getTestScores()->getValues(),$student[0]->getTestScore()->getValues(),function ($e1, $e2) { 
            if($e1->getId() == $e2->getId() ) {
                return 0;
            } else {
                return 1;
            }
            
        })){
            $this->addFlash("danger", "Becaful you have already passed this exam");
            return $this->redirectToRoute('teststudent_index');
        }
        }
        $contract=$contractRepository->findOneBy([],['id' => 'DESC']);
        //dump($contract);
        return $this->render('test/pass_exam.html.twig', [
            'controller_name' => 'PassExamController',
            'test' => $test,
            'studentId' => $student[0]->getId(),
            'username' =>$username,
            'contract'=>$contract
        ]);
    }
    
    /**
     * @Route("/level/exam/result", name="level_exam_result")
     */
    public function addScoreToLevelTest(Request $request, TestRepository $testReposetory, StudentRepository $studentRepository,\Swift_Mailer $mailer){
        $testScore = new TestScore();
        $test = $testReposetory->find($request->get('test'));
        $testScore->setValue($request->get('score'));
        $student = $studentRepository->find($request->get('studentId'));
        $testScore->setStudent($student);
        $testScore->setTest($test);
        $student->setLevelTest(true);
        //$student->setActive(0);
        $content = $this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'levelTest'])?$this->getDoctrine()->getManager()->getRepository('App:ConfigurationEmail')->findOneBy(['subject'=>'levelTest'])->getContent():"You have passed your placement test" ;
        $this->get('Emailing')->levelTestEmail($student,$this->renderView(
                        'email/emailLevelTest.html.twig',
                        ["content" => $content,"score"=>$testScore->getValue()]),$mailer);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($testScore);
        $entityManager->flush();
        $response['score']= floatval($request->get('score'));
        return new Response(json_encode($response));
    }
    /**
     * @Route("/denied/{username}", name="denied", methods={"GET"})
     */
    public function testindex(TestScoreRepository $testScoreRepository, UserRepository $userRepository,$username): Response
    {
        //$student=$studentRepository->finBy($id);
        $user = $userRepository->findBy(['email'=>$username]);
        
        $student = $user[0]->getStudent();

        $testScore=$testScoreRepository->findby(['student'=>$student[0]]);
        return $this->render('security/denied.html.twig', [
            'student'=>$student[0],
            'score'=> $testScore[0]->getValue(),
        ]);
    }
    
    /**
     * @Route("/level/test/category/{id}", name="test_level_selectCategory", methods={"GET"})
     */
    public function levelTestselectCategory(Profession $profession,Request $request): Response
    {
        $username=$request->get('username');
        $key=$request->get('key');
        $student = $this->getDoctrine()->getManager()->createQueryBuilder()->select('s')->from(Student::class, 's')->join(User::class,'u')->where('s.user = u.id and u.email = :username')->setParameter('username',$username)->getQuery()->getResult();
        if ($key!=md5($student[0]->getUser()->getEmail()))
            return $this->redirectToRoute("app_login");
        /*$categoriesUnlocked = []; 
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
        if ($this->getUser()->getStatus()=="Student"){
            $categoriesUnlocked = $this->getDoctrine()->getManager()->getRepository('App:Classes')->findBy('');
        }*/
        
        return $this->render('test/levelTestSelectCategory.html.twig', [
            'catagories' => $profession->getCategories(),
            'username' => $username,
            'key' => $key,
            /*'typeCourses' => $typeCourseRepository->findAll(),
            'students' => $studentRepository->findAll(),
            'categoriesUnlocked' => $categoriesUnlocked,*/
        ]);
    }
    /**
     * @Route("/add/level/test/{id}", name="add_level_test", methods={"GET"})
     */
    public function addLevelTest(Category $category,Request $request): Response
    {
        $key=$request->get('key');
        $username=$request->get('username');
        $student = $this->getDoctrine()->getManager()->createQueryBuilder()->select('s')->from(Student::class, 's')->join(User::class,'u')->where('s.user = u.id and u.email = :username')->setParameter('username',$username)->getQuery()->getResult();
        if ($key!=md5($student[0]->getUser()->getEmail()))
            return $this->redirectToRoute("app_login");
        $student[0]->setLevelTestType($category);
        $this->getDoctrine()->getManager()->persist($student[0]);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('pass_level_test', [
            'username' => $username,
            'key' => $key,
            /*'typeCourses' => $typeCourseRepository->findAll(),
            'students' => $studentRepository->findAll(),
            'categoriesUnlocked' => $categoriesUnlocked,*/
        ]);
    }
    /**
     * @Route("/show/pdf/test/{id}/pdf", name="show_test_pdf", methods={"GET"})
     */
    public function showTestPdf(Test $test){
        return $this->render('test/showPdf.html.twig',["supportDocument"=>$test->getSupportPdf(),'testId'=>$test->getId()]);
    }


    /**
     * @Route("/taking/{id}", name="test")
     */
    public function select(Test $test): Response
    {
        
        $today = new \DateTime() ;
        if ($test->getDeadline()>=$today){
        $student = $this->getUser()->getStudent();
        if(array_uintersect($test->getTestScores()->getValues(),$student[0]->getTestScore()->getValues(),function ($e1, $e2) { 
            if($e1->getId() == $e2->getId() ) {
                return 0;
            } else {
                return 1;
            }
            
        })){
            $this->addFlash("danger", "Becaful you have already passed this exam");
            return $this->redirectToRoute('teststudent_index',["idCategory"=>$test->getCategory()->getId(),"idClass"=>$test->getClasse()->getId()]);
        }

        
        return $this->render('test/test.html.twig', [
            'controller_name' => 'PassExamController',
            'test' => $test,
            'idCategory'=>$test->getCategory()->getId(),
            'idClass'=>$test->getClasse()->getId()
            
        ]);
        }
        $this->addFlash("danger", "You cannot take this exam, deadline exceeded");
        return $this->redirectToRoute('teststudent_index',["idCategory"=>$test->getCategory()->getId(),"idClass"=>$test->getClasse()->getId()]);
    }

     /**
     * @Route("/check/{id}/math", name="test_check_math")
     */
    public function checkmath(Test $test,QuestionRepository $questionRepository,TestScoreRepository $testScoreRepository,ReponseRepository $reponseRepository): Response
    {
        $questions=$questionRepository->findby(['test'=>$test,'type'=>'Math']);
        $result=0;
        $question=[];
        $student=$this->getUser()->getStudent();
        $reponse=$reponseRepository->findby(['test'=>$test,'student'=>$student[0]]);
        dump($reponse);
        $socrestudent=$testScoreRepository->findOneby(['student'=>$student[0],'test'=>$test]);
       // $sfinal=$socrestudent->getValue();
       $count=0;
       $i=0;
       foreach($questions as $q){
        $question[]=$q;
        
           /* if($q->getType()== "Math" ){
                if($q->getCorrectAnswer()== $reponseRepository->findoneby(['test'=>$test,'student'=>$student[0],'question'=>$q])->getReponsemath()){
                    $count=$count+1;
                }
            
        }*/
        $i=$i+1;
    }
        foreach($question as $qu){
            $score=$qu->getScore();
            $result=$result+$score;
        }
        
       
        
        return $this->render('test/check.html.twig', [
            'controller_name' => 'PassExamController',
            'test' => $test,
            'questions'=>$questions,
            'score'=>$count,
            'reponses'=>$reponse,
            'result'=>$result
            
        ]);
    }

    /**
     * @Route("/check/{id}/reading", name="test_check_reading")
     */
    public function checkreading(Test $test,QuestionRepository $questionRepository,TestScoreRepository $testScoreRepository,ReponseRepository $reponseRepository): Response
    {
        $questions=$questionRepository->findby(['test'=>$test,'type'=>'Reading']);
        $result=0;
        $question=[];
        $student=$this->getUser()->getStudent();
        $reponse=$reponseRepository->findby(['test'=>$test,'student'=>$student[0]]);
        dump($reponse);
        $socrestudent=$testScoreRepository->findOneby(['student'=>$student[0],'test'=>$test]);
       // $sfinal=$socrestudent->getValue();
       $count=0;
       $i=0;
        foreach($questions as $q){
            $question[]=$q;
            
               /* if($q->getType()== "Reading" ){
                    if($q->getCorrectChoise()== $reponseRepository->findoneby(['test'=>$test,'student'=>$student[0],'question'=>$q])->getResponse()){
                        $count=$count+1;
                    }
                
            }*/
            $i=$i+1;
        }
        foreach($question as $qu){
            $score=$qu->getScore();
            $result=$result+$score;
        }
        
       
        
        return $this->render('test/check.html.twig', [
            'controller_name' => 'PassExamController',
            'test' => $test,
            'questions'=>$questions,
            'score'=>$count,
            'reponses'=>$reponse,
            'result'=>$result
            
        ]);
    }

    /**
     * @Route("/check/{id}/writing", name="test_check_writing")
     */
    public function checkwriting(Test $test,QuestionRepository $questionRepository,TestScoreRepository $testScoreRepository,ReponseRepository $reponseRepository): Response
    {
        $questions=$questionRepository->findby(['test'=>$test,'type'=>'Writing']);
        $result=0;
        $question=[];
        $student=$this->getUser()->getStudent();
        $reponse=$reponseRepository->findby(['test'=>$test,'student'=>$student[0]]);
        dump($reponse);
        $socrestudent=$testScoreRepository->findOneby(['student'=>$student[0],'test'=>$test]);
       // $sfinal=$socrestudent->getValue();
       $count=0;
       $i=0;
        foreach($questions as $q){
            $question[]=$q;
            
              /*  if($q->getType()== "Writing" ){
                    if($q->getCorrectChoise()== $reponseRepository->findoneby(['test'=>$test,'student'=>$student[0],'question'=>$q])->getResponse()){
                        $count=$count+1;
                    }
                
            }*/
            $i=$i+1;
        }
        foreach($question as $qu){
            $score=$qu->getScore();
            $result=$result+$score;
        }
        
       
        
        return $this->render('test/check.html.twig', [
            'controller_name' => 'PassExamController',
            'test' => $test,
            'questions'=>$questions,
            'score'=>$count,
            'reponses'=>$reponse,
            'result'=>$result
            
        ]);
    }

    /**
     * @Route("/{id}/math", name="test_math", methods={"GET"})
     */
    public function math($id,QuestionRepository $questionsRepository,TestRepository $testRepository): Response
    {
        $test=$testRepository->findoneby(['id'=>$id]);
        $questions=$questionsRepository->findby(['test'=>$test,'type'=>'Math']);
       
        return $this->render('question/math.html.twig', [
            //'questions' => $questionsRepository->findAll(),
            'test'=>$test,
            'questions'=>$questions
        ]);
    }
    /**
     * @Route("/{id}/reading", name="test_reading", methods={"GET"})
     */
    public function reading($id,QuestionRepository $questionsRepository,TestRepository $testRepository): Response
    {
        $test=$testRepository->findoneby(['id'=>$id]);
        $questions=$questionsRepository->findby(['test'=>$test,'type'=>'Reading']);
       
        return $this->render('question/test.html.twig', [
            //'questions' => $questionsRepository->findAll(),
            'test'=>$test,
            'questions'=>$questions
        ]);
    }
    /**
     * @Route("/{id}/writing", name="test_writing", methods={"GET"})
     */
    public function writing($id,QuestionRepository $questionsRepository,TestRepository $testRepository): Response
    {
        $test=$testRepository->findoneby(['id'=>$id]);
        $questions=$questionsRepository->findby(['test'=>$test,'type'=>'Writing']);
       
        return $this->render('question/writing.html.twig', [
            //'questions' => $questionsRepository->findAll(),
            'test'=>$test,
            'questions'=>$questions
        ]);
    }

        /**
     * set absence status for teachers
     * @param Request $request
     * @return Response
     * @Route("/student/response", name="question_response")
     */
    public function reponseqcmAction(Request $request,QuestionRepository $questionsRepository,TestRepository $testRepository,ReponseRepository $responseRepository)
    {
        $questionid=$request->get('questionId');
        $question=$questionsRepository->findOneBy(['id'=>$questionid]);
        $test=$testRepository->findOneBy(['id'=>$question->getTest()->getId()]);
        $answer=$request->get('anwserNum');
        $answer=(int)$answer;
        $student=$this->getUser()->getStudent();
        $response=$responseRepository->findOneBy(['question'=>$question]);
        $entityManager = $this->getDoctrine()->getManager();
        if($response == null){
            $res = new Reponse();

            $res->setTest($test);
            $res->setStudent($student[0]);
            $res->setQuestion($question);
            $res->setResponse($answer);
            $entityManager->persist($res);
            $entityManager->flush();
        }
        else{
            $response->setResponse($answer);
            $response->setStudent($student[0]);
            $entityManager->persist($response);
            $entityManager->flush();
        }
       
        return new Response();
    }

    /**
     * set absence status for teachers
     * @param Request $request
     * @return Response
     * @Route("/student/response/math", name="question_response_math")
     */
    public function reponsemathAction(Request $request,QuestionRepository $questionsRepository,TestRepository $testRepository,ReponseRepository $responseRepository)
    {
        $questionid=$request->get('questionId');
        $question=$questionsRepository->findOneBy(['id'=>$questionid]);
        $test=$testRepository->findOneBy(['id'=>$question->getTest()->getId()]);
        $answer=$request->get('anwser');
        //$answer=(int)$answer;
        $student=$this->getUser()->getStudent();
        $response=$responseRepository->findOneBy(['question'=>$question,'student'=>$student[0]]);
        $entityManager = $this->getDoctrine()->getManager();
        if($response == null){
            $res = new Reponse();

            $res->setTest($test);
            $res->setStudent($student[0]);
            $res->setQuestion($question);
            $res->setReponsemath($answer);
            $entityManager->persist($res);
            $entityManager->flush();
        }
        else{
            $response->setReponsemath($answer);
            $response->setStudent($student[0]);
            $entityManager->persist($response);
            $entityManager->flush();
        }
       
        return new Response();
    }

    /**
     * @Route("/{id}/result/calc", name="test_result", methods={"GET"})
     */
    public function resultcalc($id,QuestionRepository $questionsRepository,TestRepository $testRepository,ReponseRepository $reponseRepository): Response
    {
        $test=$testRepository->findby(['id'=>$id]);
        $student=$this->getUser()->getStudent();
        $questions=$questionsRepository->findby(['test'=>$test[0]]);
       // die($questions);
        $reponses=$reponseRepository->findby(['test'=>$test,'student'=>$student[0]]);
        $score=0;
        foreach($questions as $q){
            foreach($reponses as $r){
                if($r->getQuestion()->getId()==$q->getId()){
                    if($q->getType()=="Writing"){
                       //die('wRIT');
                        $acc=$q->getAccur();
                        $note=$q->getScore();

                        $sresponse=$r->getResponse();
                        $saccur=$acc[$sresponse];
                        $score=$score+($note*$saccur/100);

                    }
                    if($q->getType()=="Reading"){
                        $acc=$q->getAccur();
                        
                        $note=$q->getScore();
                        // die(var_dump($note));
                        $sresponse=$r->getResponse();
                        $saccur=$acc[$sresponse];
                        $score=$score+($note*$saccur/100);

                    }
                    if($q->getType()=="Math"){
                        
                        
                        $note=$q->getScore();
                        // die(var_dump($note));
                        $sresponse=$r->getReponsemath();
                        
                        if($sresponse == $q->getCorrectAnswer()){
                            $score=$score+($note);
                        }
                        

                    }
                }
            }
            
        }
        return $this->render('question/resp.html.twig', [
            //'questions' => $questionsRepository->findAll(),
            'test'=>$test,
            'questions'=>$questions,
            'score'=>$score
        ]);
    }


}
