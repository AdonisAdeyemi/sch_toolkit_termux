<?php

namespace ReportCard\Models;

class StudentModel extends BaseModel
{
    protected string $table = 'report_students';

    /**
     * Get students by school
     */
    public function getBySchool(int $schoolId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} 
             WHERE school_id = ? AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$schoolId]
        );
    }

    /**
     * Get students by class
     */
    public function getByClass(int $classId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} 
             WHERE class_id = ? AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$classId]
        );
    }

    /**
     * Get students by school + class
     */
    public function getBySchoolAndClass(int $schoolId, int $classId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} 
             WHERE school_id = ? 
             AND class_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$schoolId, $classId]
        );
    }

    
}








