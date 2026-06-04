<?php
namespace ReportCard\Models;

class ClassModel extends BaseModel
{
    protected string $table = 'report_classes';

    /**
     * Get all classes for a school
     */
    public function getBySchool(int $schoolId): array
    {
        return $this->pdo->fetchAll(
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
    public function findBySchool(int $schoolId, int $classId): ?array
    {
        return $this->pdo->fetch(
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
     * Soft delete class (uses BaseModel)
     */
    
    public function softDeleteBySchool(int $schoolId, int $classId): bool
{
    return (bool) $this->pdo->query(
        "UPDATE {$this->table}
         SET is_deleted = 1
         WHERE id = ?
         AND school_id = ?",
        [$classId, $schoolId]
    );
}
    
    

    /**
     * Get class with student count (read-only query)
     */
    public function getWithStudentCount(int $schoolId): array
    {
        return $this->pdo->fetchAll(
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
    
    
  /************************************/
  public function exists(int $schoolId, string $className): bool
{
    return (bool) $this->pdo->fetch(
        "SELECT id 
         FROM {$this->table}
         WHERE school_id = ?
         AND class_name = ?
         AND (is_deleted = 0 OR is_deleted IS NULL)",
        [$schoolId, $className]
    );
}

/***************/
    
    
}












