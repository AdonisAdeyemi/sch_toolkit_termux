<?php
namespace ReportCard\Models;

use Core\Models\BaseModel;

class ClassModel extends BaseModel
{
    protected string $table = 'report_classes';

    /**
     * Get all classes for a school
     */
    public function getClassesBySchool(int $schoolId): array
    {
        return $this->fetchAll(
             
             " SELECT c.*,
            ct.label as class_name,
            ct.level as class_level
            
             FROM {$this->table} c
             JOIN report_class_templates ct
             ON ct.id = c.class_template_id 
             
             WHERE school_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$schoolId]
        );
    }

    /**
     * Get single class by id + school
     * (prevents cross-school access)
     */
    public function getClassBySchoolAndId(int $schoolId, int $classId): ?array
    {
        return $this->fetch(
            "SELECT c.*,
            ct.label as class_name,
            ct.level as class_level
            
            
             FROM {$this->table} c
             JOIN report_class_templates ct
             ON ct.id = c.class_template_id 
             
             WHERE c.id = ?
             AND c.school_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$classId, $schoolId]
        );
    }

    /**
     * Create class
     */
     
    public function create(int $schoolId, string $classTemplateId): int
    {
        return $this->insert([
            'school_id'  => $schoolId,
            'class_template_id' => $classTemplateId,
            'is_deleted' => 0
        ]);
    }

    /**
     * Soft delete class (school safe)
     */
    public function softDeleteBySchool(int $schoolId, int $classId): bool
    {
        return $this->execute(
            "UPDATE {$this->table}
             SET is_deleted = 1
             WHERE id = ?
             AND school_id = ?",
            [$classId, $schoolId]
        );
    }

/****************/

public function getDeletedClassBySchool(int $schoolId): array
{
    return $this->fetchAll(
        "SELECT c.*,
                ct.label AS class_name,
                ct.level AS class_level
         FROM {$this->table} c
         INNER JOIN class_templates ct
                ON ct.id = c.class_template_id
         WHERE c.school_id = ?
           AND c.is_deleted = 1",
        [$schoolId]
    );
}
/**********************/

    
    public function restoreClassBySchool(int $schoolId, int $classId): bool
{
    return $this->execute(
        "UPDATE {$this->table}
         SET is_deleted = 0
         WHERE id = ?
         AND school_id = ?",
        [$classId, $schoolId]
    );
}


    /**
     * Get class with student count
     */
    public function getWithStudentCount(int $schoolId): array
{
    return $this->fetchAll(
        "SELECT c.*,
                ct.label AS class_name,
                ct.level AS class_level,
                COUNT(s.id) AS student_count
         FROM {$this->table} c
         INNER JOIN class_templates ct
                ON ct.id = c.class_template_id
         LEFT JOIN report_students s
                ON s.class_id = c.id
                AND (s.is_deleted = 0 OR s.is_deleted IS NULL)
         WHERE c.school_id = ?
           AND (c.is_deleted = 0 OR c.is_deleted IS NULL)
         GROUP BY c.id, ct.label, ct.level",
        [$schoolId]
    );
}

    /**
     * Check duplicate class
     */
    public function exists(int $schoolId, int $classTemplateId): bool
{
    return (bool) $this->fetch(
        "SELECT id
         FROM {$this->table}
         WHERE school_id = ?
           AND class_template_id = ?
 
         LIMIT 1",
        [$schoolId, $classTemplateId]
    );
}
}








