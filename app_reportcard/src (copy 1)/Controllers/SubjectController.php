<?php

namespace ReportCard\Controllers;

use ReportCard\Services\SubjectService;
use Core\Controllers\BaseController;

class SubjectController extends BaseController
{
    private SubjectService $subjectService;

    public function __construct($pdo)
    {
        $this->subjectService = new SubjectService($pdo);
    }

    /**
     * Show create form
     */
public function create(array $data)
{
    $title = "Create Subject";
    $appName = $_SESSION['appName'] ?? '';

    return $this->render('admin/subjects/create', compact('title', 'appName'));
}
    /**
     * Handle form submission
     */
    public function store(array $data)
    {
        $schoolId = $_SESSION['school_id'] ?? 0;
        $name = $_POST['subject_name'] ?? '';

        $result = $this->subjectService->createSubject($schoolId, $name);
        
        if ($result['status']) {
    setFlash('success', $result['message']);
    header("Location: /".$this->appName()."/admin/subjects");
    exit;
}

// failure case
setFlash('danger', $result['message']);
    header("Location: /".$this->appName()."/admin/subjects");
exit;
        
       
    }
    
 /***************************/
 /*
 public function index(array $data)
{

$appName = $this->appName();
$title = "Subject Index";
    $schoolId = $_SESSION['school_id'] ?? 0;

    $subjects = $this->subjectService->getSubjects($schoolId);

    return $this->render('admin/subjects/index', compact('title','subjects','schoolId','appName'));
}
*/

public function index(array $data)
{
    $appName = $this->appName();
    $title = "Subject Index";
    $schoolId = $_SESSION['school_id'] ?? 0;

    $activeSubjects = $this->subjectService->getActiveSubjects($schoolId);
    $deletedSubjects = $this->subjectService->getDeletedSubjects($schoolId);

    return $this->render(
        'admin/subjects/index',
        compact('title', 'activeSubjects', 'deletedSubjects', 'schoolId', 'appName')
    );
}
    
/***************************/

public function edit(array $data)
{
    $schoolId = $_SESSION['school_id'] ?? 0;
    $id = $data['get']['id']  ?? 0;
    

    $subject = $this->subjectService->getSubject($schoolId, $id);

    if (!$subject) {
        setFlash('danger', 'Subject not found');
        header("Location: /".$this->appName()."/subjects");
        exit;
    }

    return $this->render('subjects/edit', compact('subject'));
}


/********************/

public function update(array $data)
{
    $schoolId = $_SESSION['school_id'];

    $id = $data['post']['id'] ?? 0;
    $name = $data['post']['subject_name']  ?? null;


    $result = $this->subjectService->updateSubject($schoolId, $id, $name);

    setFlash(
        $result['status'] ? 'success' : 'danger',
        $result['message']
    );

    header("Location: /".$this->appName()."/admin/subjects");
    exit;
}

/************************/

public function delete(array $data)
{
    $schoolId = $_SESSION['school_id'] ?? 0;
    $id = $data['post']['id'] ?? 0;

    $result = $this->subjectService->deleteSubject($schoolId, $id);

    setFlash(
        $result['status'] ? 'success' : 'danger',
        $result['message']
    );

    header("Location: /".$this->appName()."/admin/subjects");
    exit;
}



/****************/

public function restore(array $request)
{
    $schoolId = $_SESSION['school_id'] ?? 0;

    $subjectId = (int)($request['post']['id'] ?? 0);
    


    $result = $this->subjectService->restoreSubject($schoolId, $subjectId);

    setFlash(
        $result['status'] ? 'success' : 'danger',
        $result['message']
    );

    header("Location: /{$this->appName()}/admin/subjects");
    exit;
}





    
    
}









