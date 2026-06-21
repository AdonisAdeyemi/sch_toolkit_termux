<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;

class StudentModel extends BaseModel
{
    protected string $table = 'report_students';

    /**
     * Get single student
     */
    public function getStudentById(int $studentId): ?array
    {
        return $this->fetch(
            "
            SELECT *
            FROM report_students
            WHERE id = ?
            LIMIT 1
            ",
            [$studentId]
        );
    }

    /**
     * Get class details from class ID
     */
    public function getClassById(int $classId): ?array
    {
        return $this->fetch(
            "
            SELECT
                rc.*,
                rct.label AS class_name,
                rct.code,
                rct.level
            FROM report_classes rc
            LEFT JOIN report_class_templates rct
                ON rct.id = rc.class_template_id
            WHERE rc.id = ?
            LIMIT 1
            ",
            [$classId]
        );
    }

    /**
     * Get all students in a class
     */
    public function getStudentsByClassId(int $classId): array
    {
        return $this->fetchAll(
            "
            SELECT *
            FROM report_students
            WHERE class_id = ?
              AND is_deleted = 0
            ORDER BY student_name
            ",
            [$classId]
        );
    }
    
    
  /**********
  *****/  
    public function getClassIdByStudentId(int $studentId): ?int
{

    if (!$studentId) return null;

    return (int) $this->fetch(
        "
        SELECT class_id
        FROM report_students
        WHERE id = ?
          AND is_deleted = 0
        LIMIT 1
        ",
        [$studentId]
    );
}

    /**
     * Get student count for class
     */
    public function countStudentsByClassId(int $classId): int
    {
        return (int) $this->fetchColumn(
            "
            SELECT COUNT(*)
            FROM report_students
            WHERE class_id = ?
              AND is_deleted = 0
            ",
            [$classId]
        );
    }

    /**
     * Get student with class details
     */
    public function getStudentWithClass(int $studentId): ?array
    {
        return $this->fetch(
            "
            SELECT
                rs.*,
                rct.label AS class_name,
                rct.code,
                rct.level
            FROM report_students rs
            LEFT JOIN report_classes rc
                ON rc.id = rs.class_id
            LEFT JOIN report_class_templates rct
                ON rct.id = rc.class_template_id
            WHERE rs.id = ?
            LIMIT 1
            ",
            [$studentId]
        );
    }
}
