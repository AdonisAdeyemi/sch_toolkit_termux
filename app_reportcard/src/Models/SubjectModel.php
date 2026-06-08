<?php

namespace ReportCard\Models;

class SubjectModel extends BaseModel
{
    protected string $table = 'report_subjects';

    /**
     * Get all subjects for a school
     */
     // currently unused
     /*
    public function getSubjectsBySchool(int $schoolId): array
    {
  return $this->fetchAll(
    "SELECT rs.*,
            COALESCE(s.name, rs.subject_name) AS subject_name
     FROM {$this->table} rs
     LEFT JOIN subjects s
            ON s.id = rs.base_subject_id
     WHERE (
            (rs.school_id = ? AND rs.is_deleted = 0)
            OR (rs.school_id IS NULL AND rs.is_custom = 0)
           )
     ORDER BY is_custom, subject_name ASC",
    [$schoolId]
);

    }
    */

/***********/

public function getActiveSubjects(int $schoolId): array
{
    return $this->fetchAll(
        "SELECT rs.*,
                COALESCE(s.name, rs.subject_name) AS subject_name
         FROM {$this->table} rs
         LEFT JOIN subjects s
                ON s.id = rs.base_subject_id
         WHERE (
                (rs.school_id = ? AND rs.is_deleted = 0)
                OR (rs.school_id IS NULL AND rs.is_custom = 0)
               )
         ORDER BY is_custom, subject_name ASC",
        [$schoolId]
    );
}

/*************/


public function getDeletedSubjects(int $schoolId): array
{
    return $this->fetchAll(
        "SELECT rs.*,
                COALESCE(s.name, rs.subject_name) AS subject_name
         FROM {$this->table} rs
         LEFT JOIN subjects s
                ON s.id = rs.base_subject_id
         WHERE rs.school_id = ?
         AND rs.is_deleted = 1
         ORDER BY subject_name ASC",
        [$schoolId]
    );
}


    /**
     * Get single subject by school + id
     */
    public function getSubjectById(int $schoolId, int $subjectId): ?array
    {
        return $this->fetch(
            "SELECT *
             FROM {$this->table}
             WHERE id = ?
             AND school_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)
             LIMIT 1",
            [$subjectId, $schoolId]
        );
    }




    /**
     * Create subject
     */
    public function createSubject(int $schoolId, string $subjectName): int
    {
        return $this->insert([
            'school_id'    => $schoolId,
            'subject_name' => $subjectName,
            'is_deleted'   => 0,
            'is_custom' => 1
        ]);
    }

    /**
     * Check if subject exists in school
     */
    public function subjectExists(int $schoolId, string $subjectName): bool
    {
        return (bool) $this->fetch(
            "SELECT id
             FROM {$this->table}
             WHERE school_id = ?
             AND subject_name = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)
             LIMIT 1",
            [$schoolId, $subjectName]
        );
    }
    
/********************************/

public function updateSubject(int $schoolId, int $id, string $subjectName): bool
{
    return $this->execute(
        "UPDATE {$this->table}
         SET subject_name = ?
         WHERE id = ?
         AND school_id = ?
         AND is_custom = 1
         AND (is_deleted = 0 OR is_deleted IS NULL)",
        [$subjectName, $id, $schoolId]
    );
}


/***********************/

public function softDeleteSubject(int $schoolId, int $id): bool
{
    return $this->execute(
        "UPDATE {$this->table}
         SET is_deleted = 1
         WHERE id = ?
         AND school_id = ?
         AND is_custom = 1",
        [$id, $schoolId]
    );
}

/********************/


public function restoreSubject(int $schoolId, int $subjectId): bool
{
    return $this->execute(
        "UPDATE {$this->table}
         SET is_deleted = 0
         WHERE id = ?
         AND school_id = ?
         AND is_custom = 1",
        [$subjectId, $schoolId]
    );
}




}


















