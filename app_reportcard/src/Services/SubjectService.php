<?php

namespace ReportCard\Services;

use ReportCard\Models\SubjectModel;

class SubjectService
{
    private SubjectModel $subjectModel;

    public function __construct($pdo)
    {
        $this->subjectModel = new SubjectModel($pdo);
    }

    /**
     * Get all subjects
     */
    public function getSubjects(int $schoolId): array
    {
        return $this->subjectModel->getSubjectsBySchool($schoolId);
    }

    /**
     * Create subject (business logic layer)
     */
    public function createSubject(int $schoolId, string $subjectName): array
    {
        // 1. Clean input
        $subjectName = trim($subjectName);

        // 2. Validate
        if ($subjectName === '') {
            return [
                'status' => false,
                'message' => 'Subject name cannot be empty'
            ];
        }

        // 3. Prevent duplicates
        if ($this->subjectModel->subjectExists($schoolId, $subjectName)) {
            return [
                'status' => false,
                'message' => 'Subject already exists'
            ];
        }

        // 4. Save
        $id = $this->subjectModel->createSubject($schoolId, $subjectName);

        return [
            'status' => true,
            'message' => 'Subject created successfully',
            'id' => $id
        ];
    }
    
 /*************************/
 
 public function updateSubject(int $schoolId, int $id, string $name): array
{
    $name = trim($name);

    if ($name === '') {
        return ['status' => false, 'message' => 'Subject name required'];
    }

    $subject = $this->subjectModel->getSubjectById($schoolId, $id);

    if (!$subject) {
        return ['status' => false, 'message' => 'Subject not found'];
    }

    if ((int)$subject['is_custom'] === 0) {
        return ['status' => false, 'message' => 'Default subject cannot be edited'];
    }

    $this->subjectModel->updateSubject($schoolId, $id, $name);

    return ['status' => true, 'message' => 'Subject updated'];
}   
    
/*******************/

public function deleteSubject(int $schoolId, int $id): array
{
    $subject = $this->subjectModel->getSubjectById($schoolId, $id);

    if (!$subject) {
        return ['status' => false, 'message' => 'Subject not found'];
    }

    if ((int)$subject['is_custom'] === 0) {
        return ['status' => false, 'message' => 'Default subject cannot be deleted'];
    }

    $this->subjectModel->softDeleteSubject($schoolId, $id);

    return ['status' => true, 'message' => 'Subject deleted'];
}



    
}














