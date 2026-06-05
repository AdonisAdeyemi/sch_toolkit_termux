<?php

namespace ReportCard\Controllers;

use ReportCard\Models\ClassModel;
use Core\Controllers\BaseController;

class ClassController extends BaseController
{
    private ClassModel $classModel;
    private $appName;

    public function __construct($pdo)
    {
        $this->classModel = new ClassModel($pdo);
        $this->appName = $_SESSION["appName"] ?? null ;
    }


public function index($data)
{
    $schoolId = $_SESSION['school_id'];

    $activeClasses = $this->classModel->getWithStudentCount($schoolId);
    $deletedClasses = $this->classModel->getDeletedBySchool($schoolId);

/*
    include __DIR__ . '/../Views/admin/classes/index.php';
    */
    
 
$this->render('admin/classes/index', [
'appName' =>  $this->appName ,
'title' => 'Class List',
'schoolId' =>  $schoolId ,
'activeClasses' =>  $activeClasses ,
'deletedClasses' =>  $deletedClasses 
]);
  
    
}



    /**
     * CREATE CLASS
     */
    public function store($data)
    {
        $schoolId = $_SESSION['school_id'];

        $className = trim($data['post']['class_name'] ?? '');

        if ($className === '') {
            
        setFlash ('danger','Class name is required');    
            
            header("Location: /{$this->appName}/admin/classes");
            exit;
        }

        if ($this->classModel->exists($schoolId, $className)) {

               setFlash ('danger','Class already exists'); 
               
            header("Location: /{$this->appName}/admin/classes");
            exit;
        }

        $this->classModel->create($schoolId, $className);

        setFlash ('success','Class created successfully'); 
        header("Location: /{$this->appName}/admin/classes");
        exit;
    }

    /**
     * DELETE CLASS
     */
    public function delete($data)
    {
        $schoolId = $_SESSION['school_id'];

        $id = (int) ($data['post']['id'] ?? 0);

        if ($id <= 0) {
            
        setFlash ('danger','Invalid class ID'); 
        
                   
            header("Location: /{$this->appName}/admin/classes");
            exit;
        }

        $this->classModel->softDeleteBySchool($schoolId, $id);

        setFlash ('success','Class deleted successfully');    
        
        header("Location: /{$this->appName}/admin/classes");
        exit;
    }
    
    /***********************/
    
 
 
  /**************/
  
  public function restore($data)
{
    $schoolId = $_SESSION['school_id'];

    $id = (int) ($data['post']['id'] ?? 0);

    if ($id <= 0) {

   setFlash ('danger','Invalid class ID'); 
   
        header("Location: /{$this->appName}/admin/classes");
        exit;
    }

    $this->classModel->restoreBySchool($schoolId, $id);

       setFlash ('success','Class restored successfully'); 
    
    header("Location: /{$this->appName}/admin/classes");
    exit;
}
    
    
}









