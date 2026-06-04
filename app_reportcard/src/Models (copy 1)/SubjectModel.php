<?php

namespace ReportCard\Models;

class SubjectModel extends BaseModel
{
    protected string $table = 'report_subjects';

    /**
     * Get all subjects (global + school-specific)
     */
    public function getAllSubjects(int $schoolId): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM {$this->table}
             WHERE school_id IS NULL
                OR school_id = ?
                AND (is_deleted = 0 OR is_deleted IS NULL)
             ORDER BY name ASC",
            [$schoolId]
        );
    }

    /**
     * Get ONLY global subjects (WAEC/system subjects)
     */
    public function getGlobalSubjects(): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM {$this->table}
             WHERE school_id IS NULL
             AND (is_deleted = 0 OR is_deleted IS NULL)
             ORDER BY name ASC"
        );
    }

    /**
     * Get ONLY school custom subjects
     */
    public function getSchoolSubjects(int $schoolId): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM {$this->table}
             WHERE school_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)
             ORDER BY name ASC",
            [$schoolId]
        );
    }

    /**
     * Find subject safely (global or school)
     */
    public function findByIdForSchool(int $id, int $schoolId): ?array
    {
        return $this->db->fetch(
            "SELECT *
             FROM {$this->table}
             WHERE id = ?
             AND (school_id IS NULL OR school_id = ?)
             AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$id, $schoolId]
        );
    }

    /**
     * Create school custom subject
     */
    public function createSchoolSubject(int $schoolId, string $name): int
    {
        return $this->insert([
            'school_id'       => $schoolId,
            'base_subject_id' => null,
            'name'            => $name,
            'is_custom'       => 1,
            'is_deleted'      => 0
        ]);
    }

    /**
     * Create global subject (admin/system only)
     */
    public function createGlobalSubject(string $name, int $baseSubjectId = null): int
    {
        return $this->insert([
            'school_id'       => null,
            'base_subject_id' => $baseSubjectId,
            'name'            => $name,
            'is_custom'       => 0,
            'is_deleted'      => 0
        ]);
    }

    /**
     * Soft delete subject
     */
    public function deleteSubject(int $id): bool
    {
        return $this->softDelete($id);
    }
}
