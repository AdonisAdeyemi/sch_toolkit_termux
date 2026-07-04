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
    
    
    /**********************/
    /*
    public function getClassLevelByClassId(
    int $classId
): ?string
{
    $row = $this->fetch(
        "
        SELECT
            ct.class_level
        FROM report_classes c
        INNER JOIN report_class_templates ct
            ON ct.id = c.class_template_id
        WHERE
            c.id = ?
        LIMIT 1
        ",
        [
            $classId
        ]
    );

    return $row['class_level'] ?? null;
}
*/

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
         INNER JOIN report_class_templates ct
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
    public function getWithStudentCount(int $sessionId, int $schoolId): array
{

return $this->fetchAll(
    "SELECT
        c.*,
        ct.label AS class_name,
        ct.level AS class_level,
        COUNT(s.id) AS student_count

     FROM {$this->table} c

     INNER JOIN report_class_templates ct
        ON ct.id = c.class_template_id

     LEFT JOIN report_student_enrollments se
        ON se.class_id = c.id
       AND se.session_id = ?

     LEFT JOIN report_students s
        ON s.id = se.student_id
       AND (s.is_deleted = 0 OR s.is_deleted IS NULL)

     WHERE c.school_id = ?
       AND (c.is_deleted = 0 OR c.is_deleted IS NULL)

     GROUP BY c.id",
    [$sessionId, $schoolId]
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

/**********/

public function getClassesWithLevels(
    int $schoolId
): array
{
    $rows = $this->fetchAll(
        "
        SELECT

            c.id,

            ct.label as class_name,

            ct.level as class_level

        FROM report_classes c

        INNER JOIN report_class_templates ct

            ON ct.id = c.class_template_id

        WHERE

            c.school_id = ?

        ORDER BY

            ct.sort_order,

            ct.label
        ",
        [
            $schoolId
        ]
    );

    $result = [];

    foreach ($rows as $row) {

        $result[$row['id']] = [

            'class_name' =>
                $row['class_name'],

            'class_level' =>
                $row['class_level']

        ];

    }

    return $result;
}


/************************/

   public function getClassTemplates(): bool
{
    return (bool) $this->fetch(
"
    SELECT id, code, label, level, sort_order
    FROM report_class_templates
    ORDER BY level, sort_order ASC
"
    );
}



/*****/
    /**
     * Get class details from class ID
     */
     /*
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
    */



}















