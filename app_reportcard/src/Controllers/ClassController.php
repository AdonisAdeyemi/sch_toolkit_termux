<?php

namespace ReportCard\Controllers;

use ReportCard\Models\ClassModel;

class ClassController
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

    include __DIR__ . '/../Views/admin/classes/index.php';
}



    /**
     * CREATE CLASS
     */
    public function store($data)
    {
        $schoolId = $_SESSION['school_id'];

        $className = trim($data['post']['class_name'] ?? '');

        if ($className === '') {
            $_SESSION['error'] = "Class name is required";
            header("Location: /{$this->appName}/admin/classes");
            exit;
        }

        if ($this->classModel->exists($schoolId, $className)) {
            $_SESSION['error'] = "Class already exists";
            header("Location: /{$this->appName}/admin/classes");
            exit;
        }

        $this->classModel->create($schoolId, $className);

        $_SESSION['success'] = "Class created successfully";
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
            $_SESSION['error'] = "Invalid class ID";
            header("Location: /{$this->appName}/admin/classes");
            exit;
        }

        $this->classModel->softDeleteBySchool($schoolId, $id);

        $_SESSION['success'] = "Class deleted";
        header("Location: /{$this->appName}/admin/classes");
        exit;
    }
    
    /***********************/
    
    public function deleted($data)
{
    $schoolId = $_SESSION['school_id'];

    $classes = $this->classModel->getDeletedBySchool($schoolId);

    include __DIR__ . '/../Views/admin/classes/deleted.php';
}
 
  /**************/
  
  public function restore($data)
{
    $schoolId = $_SESSION['school_id'];

    $id = (int) ($data['post']['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['error'] = "Invalid class ID";
        header("Location: /admin/classes/deleted");
        exit;
    }

    $this->classModel->restoreBySchool($schoolId, $id);

    $_SESSION['success'] = "Class restored successfully";
    header("Location: /admin/classes/deleted");
    exit;
}
    
    
}









