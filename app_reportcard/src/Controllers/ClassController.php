<?php

namespace ReportCard\Controllers;

use ReportCard\Models\ClassModel;
use Core\Controllers\BaseController;
use ReportCard\Models\SchoolPeriodSettingsModel;
use PDO;

/*

use ReportCard\Models\SchoolPeriodSettingsModel;
use ReportCard\Models\AcademicPeriodModel;
use Core\Controllers\BaseController;
use PDO;

class SchoolPeriodSettingsController extends BaseController
{
    private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;
*/

class ClassController extends BaseController
{
    private ClassModel $classModel;
        private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;
    private $appName;
    private $pdo;

    public function __construct($pdo)
    {
        $this->classModel = new ClassModel($pdo);
        $this->appName = $_SESSION["appName"] ?? null ;
       $this->pdo = $pdo; 
        $this->schoolPeriodSettingsModel = new SchoolPeriodSettingsModel($pdo);
    }


public function index($data)
{
    $schoolId = $this->schoolId();
    
$activeSessionRow = $this->schoolPeriodSettingsModel->getActivePeriod($schoolId) ;
$activeSessionId  = $activeSessionRow['session_id'] ?? 0;

    $activeClasses = $this->classModel->getWithStudentCount($activeSessionId, $schoolId);
    $deletedClasses = $this->classModel->getDeletedClassBySchool($schoolId);
    
    
$classTemplates = $this->classModel->getClassTemplates();


    


 
$this->render('admin/classes/index', [
'appName' =>  $this->appName ,
'title' => 'Class List',
'schoolId' =>  $schoolId ,
'activeClasses' =>  $activeClasses ,
'deletedClasses' =>  $deletedClasses,
'classTemplates' => $classTemplates
]);
  
    
}



    /**
     * CREATE CLASS
     */
    public function store($data)
    {
     $schoolId = $this->schoolId();

        $classTemplateId = trim($data['post']['class_template_id'] ?? 0);
        
        echo "in classController >> classTemplateId :".$classTemplateId ;

        if ($classTemplateId === 0) {
            
        setFlash ('danger','Class is required');    
            
            header("Location: /{$this->appName()}/admin/classes");
            exit;
        }
        

        if ($this->classModel->exists($schoolId, $classTemplateId)) {

               setFlash ('danger','Class already exists'); 
               
            header("Location: /{$this->appName()}/admin/classes");
            exit;
        }

        $this->classModel->create($schoolId, $classTemplateId);

        setFlash ('success','Class created successfully'); 
        header("Location: /{$this->appName()}/admin/classes");
        exit;
    }

    /**
     * DELETE CLASS
     */
    public function delete($data)
    {
$schoolId = $this->schoolId();

        $id = (int) ($data['post']['id'] ?? 0);

        if ($id <= 0) {
            
        setFlash ('danger','Invalid class ID'); 
        
                   
            header("Location: /{$this->appName()}/admin/classes");
            exit;
        }

        $this->classModel->softDeleteBySchool($schoolId, $id);

        setFlash ('success','Class deleted successfully');    
        
        header("Location: /{$this->appName()}/admin/classes");
        exit;
    }
    
    /***********************/
    
 
 
  /**************/
  
  public function restore($data)
{
$schoolId = $this->schoolId();

    $id = (int) ($data['post']['id'] ?? 0);

    if ($id <= 0) {

   setFlash ('danger','Invalid class ID'); 
   
        header("Location: /{$this->appName()}/admin/classes");
        exit;
    }

    $this->classModel->restoreClassBySchool($schoolId, $id);

       setFlash ('success','Class restored successfully'); 
    
    header("Location: /{$this->appName()}/admin/classes");
    exit;
}
    
    
}









