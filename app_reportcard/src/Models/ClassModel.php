<?php

namespace ReportCard\Models;

class ClassModel extends BaseModel
{
    protected string $table = 'report_classes';

    /**
     * Get all classes for a school
     */
    public function getClassesBySchool(int $schoolId): array
    {
        return $this->fetchAll(
            "SELECT *
             FROM {$this->table}
             WHERE school_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$schoolId]
        );
    }

    /**
     * Get single class by id + school
     * (prevents cross-school access)
     */
    public function findClassBySchool(int $schoolId, int $classId): ?array
    {
        return $this->fetch(
            "SELECT *
             FROM {$this->table}
             WHERE id = ?
             AND school_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$classId, $schoolId]
        );
    }

    /**
     * Create class
     */
    public function create(int $schoolId, string $className): int
    {
        return $this->insert([
            'school_id'  => $schoolId,
            'class_name' => $className,
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
        "SELECT *
         FROM {$this->table}
         WHERE school_id = ?
         AND is_deleted = 1",
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
                    COUNT(s.id) AS student_count
             FROM {$this->table} c
             LEFT JOIN report_students s
                    ON s.class_id = c.id
                    AND (s.is_deleted = 0 OR s.is_deleted IS NULL)
             WHERE c.school_id = ?
             AND (c.is_deleted = 0 OR c.is_deleted IS NULL)
             GROUP BY c.id",
            [$schoolId]
        );
    }

    /**
     * Check duplicate class
     */
    public function exists(int $schoolId, string $className): bool
    {
        return (bool) $this->fetch(
            "SELECT id 
             FROM {$this->table}
             WHERE school_id = ?
             AND class_name = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)
             LIMIT 1",
            [$schoolId, $className]
        );
    }
}
