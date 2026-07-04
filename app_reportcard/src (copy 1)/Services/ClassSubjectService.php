<?php

namespace ReportCard\Services;

use ReportCard\Models\ClassSubjectModel;
use ReportCard\Models\ReportResultModel;
use PDO;

class ClassSubjectService
{
private ClassSubjectModel $classSubjectModel;
private ReportResultModel $reportResultModel;
const GENERAL_DEPT_ID = 1;

    public function __construct(PDO $pdo) {
    $this->classSubjectModel = new ClassSubjectModel($pdo);
$this->reportResultModel = new ReportResultModel($pdo);
    }

/*
    public function sync(int $schoolId, int $classId, array $newSubjectIds): array
    {
        $existing = $this->classSubjectModel
            ->getClassSubjectIds($schoolId, $classId);

        $toAdd = array_diff($newSubjectIds, $existing);
        $toRemove = array_diff($existing, $newSubjectIds);

        $blocked = [];

        // 1. Check safety before removing
        foreach ($toRemove as $subjectId) {

            $hasResults = $this->reportResultModel
                ->existsByClassSubject($schoolId, $classId, $subjectId);

            if ($hasResults) {
                $blocked[] = $subjectId;
                continue;
            }

            $this->classSubjectModel->deleteSubject(
                $schoolId,
                $classId,
                $subjectId
            );
        }

        // 2. Add new subjects
        foreach ($toAdd as $subjectId) {
            $this->classSubjectModel->insertSubject(
                $schoolId,
                $classId,
                (int)$subjectId
            );
        }

        return [
            'added'   => array_values($toAdd),
            'removed' => array_values($toRemove),
            'blocked' => $blocked
        ];
    }
    */
    
    
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

foreach ($toAdd as $subjectId) {

    $departmentId = isset($departments[$subjectId])
        ? (int)$departments[$subjectId]
        : self::GENERAL_DEPT_ID ; 

    $this->classSubjectModel->insert([
        'school_id' => $schoolId,
        'class_id' => $classId,
        'report_subject_id' => $subjectId,
        'department_id' => $departmentId
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
        : self::GENERAL_DEPT_ID;

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













