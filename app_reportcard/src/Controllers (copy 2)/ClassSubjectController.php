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
    private ClassSubjectService $service;
 private DepartmentModel $departmentModel;
private ClassSubjectModel $classSubjectModel;


    public function __construct(PDO $pdo)
    {
        $this->classModel = new ClassModel($pdo);
        $this->subjectModel = new SubjectModel($pdo);
        $this->service = new ClassSubjectService($pdo);
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

    $result = $this->service->sync(
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

    $result = $this->service->sync(
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




}








