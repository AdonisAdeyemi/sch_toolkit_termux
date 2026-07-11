<?php

namespace ReportCard\Services;

use ReportCard\Models\SubjectModel ;
use ReportCard\Models\ClassSubjectModel;
use ReportCard\Models\ReportResultModel;
use ReportCard\Models\DepartmentSubdivisionModel;
use PDO;

use ReportCard\Core\Constants;

class ClassSubjectService
{
private ClassSubjectModel $classSubjectModel;
private ReportResultModel $reportResultModel;
private DepartmentSubdivisionModel $departmentSubdivisionModel;
private SubjectModel $subjectModel;


   public function __construct(PDO $pdo) {
    $this->classSubjectModel = new ClassSubjectModel($pdo);
$this->reportResultModel = new ReportResultModel($pdo);
$this->departmentSubdivisionModel = new DepartmentSubdivisionModel($pdo);
$this->subjectModel = new SubjectModel($pdo);

    }


    
    public function sync(
    int $schoolId,
    int $classId,
    array $selectedSubjects,
    array $departments
): array
{
    $current = $this->classSubjectModel
        ->getAssignmentsMap(
            $schoolId,
            $classId
        );

    $currentIds = array_keys($current);

    $newIds = array_map(
        'intval',
        $selectedSubjects
    );

    $toAdd = array_diff(
        $newIds,
        $currentIds
    );

    $toRemove = array_diff(
        $currentIds,
        $newIds
    );

    $toKeep = array_intersect(
        $currentIds,
        $newIds
    );

    // add new subjects
    $added = [];


//refactor later - use constant eg. IRS_SUBJECT_ID 
//change of name in db wont aftect id
//same issue in classSubjectController
/*
$crsSubjectId = $this->subjectModel
    ->findSubjectIdByName("Christian Religious Studies (CRS)") ;


$irsSubjectId = $this->subjectModel
    ->findSubjectIdByName("Islamic Religious Studies (IS)");
*/

$crsSubjectId = Constants::CRS_SUBJECT_ID;
$irsSubjectId = Constants::IRS_SUBJECT_ID;

// should this crsSubdivisionId be made into CONSTANT like crsSubjectId
//is there risk of db "NAME" changing
$crsSubdivisionId = $this->departmentSubdivisionModel
    ->getSubdivisionIdByName("CRS");

$irsSubdivisionId = $this->departmentSubdivisionModel
    ->getSubdivisionIdByName("IRS");


foreach ($toAdd as $subjectId) {

    $departmentId = isset($departments[$subjectId])
        ? (int) $departments[$subjectId]
        : Constants::GENERAL_DEPT_ID;

    $departmentSubdivisionId = null;

    /*
    |--------------------------------------------------------------------------
    | Temporary business rule
    |--------------------------------------------------------------------------
    | Arts Department:
    | - Christian Religious Studies (CRS) -> CRS subdivision
    | - Islamic Religious Studies (IRS)   -> IRS subdivision
    */
    if ($departmentId === Constants::ARTS_DEPT_ID) {

        if ($subjectId === $crsSubjectId) {

            $departmentSubdivisionId = $crsSubdivisionId;

        } elseif ($subjectId === $irsSubjectId) {

            $departmentSubdivisionId = $irsSubdivisionId;
        }
    }

    $this->classSubjectModel->insert([
        'school_id'                 => $schoolId,
        'class_id'                  => $classId,
        'report_subject_id'         => $subjectId,
        'department_id'             => $departmentId,
        'department_subdivision_id' => $departmentSubdivisionId
    ]);

    $added[] = $subjectId;
}




    // remove deselected subjects
$removed = [];
$blocked = [];

foreach ($toRemove as $subjectId) {

    $classSubjectId =
        $current[$subjectId]['class_subject_id'];

    $hasResults = $this->reportResultModel
        ->existsForClassSubject($classSubjectId);

    if ($hasResults) {

        $blocked[] = $subjectId;

        continue;
    }

    $this->classSubjectModel
        ->deleteById($classSubjectId);

    $removed[] = $subjectId;
}
    // update department for kept subjects
$updated = [];

foreach ($toKeep as $subjectId) {

    $newDepartmentId = isset($departments[$subjectId])
        ? (int)$departments[$subjectId]
        : Constants::GENERAL_DEPT_ID;

    $oldDepartmentId =
        (int)$current[$subjectId]['department_id'];

    if ($oldDepartmentId === $newDepartmentId) {
        continue;
    }

    $classSubjectId =
        $current[$subjectId]['class_subject_id'];

    $this->classSubjectModel
        ->updateDepartment(
            $classSubjectId,
            $newDepartmentId
        );

    $updated[] = $subjectId;
}


    return [
        'added' => $added,
        'removed' => $removed,
        'updated' => $updated,
        'blocked' => $blocked
    ];
}
    
    
    
    /**************/
    
    public function getAssignedSubjectIds(int $schoolId, int $classId): array
{
    return $this->classSubjectModel
        ->getClassSubjectIds($schoolId, $classId);
}
    
    
}













