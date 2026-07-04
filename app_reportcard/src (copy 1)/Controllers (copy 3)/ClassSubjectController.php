<?php

namespace ReportCard\Controllers;

use ReportCard\Models\ClassModel;
use ReportCard\Models\SubjectModel;
use ReportCard\Models\DepartmentModel;
use ReportCard\Models\ClassSubjectModel;
use ReportCard\Services\ClassSubjectService;

use Core\Controllers\BaseController;
use PDO;

class ClassSubjectController extends BaseController
{
private ClassModel $classModel;
    private SubjectModel $subjectModel;
    private ClassSubjectService $classSubjectService;
 private DepartmentModel $departmentModel;
private ClassSubjectModel $classSubjectModel;


    public function __construct(PDO $pdo)
    {
        $this->classModel = new ClassModel($pdo);
        $this->subjectModel = new SubjectModel($pdo);
        $this->classSubjectService = new ClassSubjectService($pdo);
                $this->classSubjectModel = new ClassSubjectModel($pdo);
        $this->departmentModel = new DepartmentModel($pdo);
    }

    public function edit($data, int $classId)
    {
        $schoolId = $this->schoolId();

        $class = $this->classModel->getClassBySchoolAndId($schoolId, $classId);
$subjects = $this->classSubjectModel
    ->getSubjectAssignmentsForClass(
        $schoolId,
        $classId
    );

        $departments = $this->departmentModel
    ->getAll($schoolId);

            $title = "Pick Subjects for Each Class";



        return $this->render('admin/class_subjects/edit', [
        'title' => $title,
        'appName' => $this->appName(),
            'class'    => $class,
            'subjects' => $subjects,
            'departments' => $departments
        ]);
    }

/*
    public function update($data, int $classId)
{
        $schoolId = $this->schoolId();

    $subjectIds = $_POST['subjects'] ?? [];

    $result = $this->classSubjectService->sync(
        $schoolId,
        $classId,
        array_map('intval', $subjectIds)
    );

    return $this->json([
        'status' => 'success',
        'added'   => $result['added'],
        'removed' => $result['removed'],
        'blocked' => $result['blocked']
    ]);
}


/************/
public function update(
    array $request,
    int $classId
)
{
    $schoolId = $this->schoolId();

    $subjects =
        $request['post']['subjects'] ?? [];

    $departments =
        $request['post']['department'] ?? [];

    $result = $this->classSubjectService->sync(
        $schoolId,
        $classId,
        $subjects,
        $departments
    );


    echo json_encode([
        'status' => 'success',
        ...$result
    ]);
}

/************************/

public function subjectListOfClass($data, $classId)
{
    header('Content-Type: application/json');


    try {
        $schoolId = $_SESSION['school_id'];

        $subjects = $this->classSubjectModel->getByClass($schoolId, (int)$classId);
        
    //  var_dump("in classSubjCntrlr > subject for dropdwon list", $subjects);

        echo json_encode([
            'status' => 'success',
            'data' => $subjects
        ]);

    } catch (Exception $e) {

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}





}













