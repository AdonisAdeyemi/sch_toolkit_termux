<?php

namespace ReportCard\Models;

class ClassSubjectModel extends BaseModel
{
    protected string $table = 'report_class_subjects';

    /**
     * Get all subjects for a class
     */
    public function getByClass(int $classId): array
    {
        return $this->db->fetchAll(
            "SELECT cs.*,
                    s.name AS subject_name
             FROM {$this->table} cs
             JOIN report_subjects s ON s.id = cs.report_subject_id
             WHERE cs.class_id = ?
             AND (cs.is_deleted = 0 OR cs.is_deleted IS NULL)
             ORDER BY cs.subject_order ASC",
            [$classId]
        );
    }

    /**
     * Get all subjects for a school (across classes)
     */
    public function getBySchool(int $schoolId): array
    {
        return $this->db->fetchAll(
            "SELECT cs.*,
                    s.name AS subject_name,
                    c.class_name
             FROM {$this->table} cs
             JOIN report_subjects s ON s.id = cs.report_subject_id
             JOIN report_classes c ON c.id = cs.class_id
             WHERE cs.school_id = ?
             AND (cs.is_deleted = 0 OR cs.is_deleted IS NULL)
             ORDER BY c.class_name, cs.subject_order ASC",
            [$schoolId]
        );
    }

    /**
     * Assign subject to class
     */
    public function assignSubject(
        int $schoolId,
        int $classId,
        int $subjectId,
        string $aliasName = null,
        int $order = 0,
        int $departmentId = null
    ): int {
        return $this->insert([
            'school_id'          => $schoolId,
            'class_id'           => $classId,
            'report_subject_id'  => $subjectId,
            'alias_name'         => $aliasName,
            'subject_order'      => $order,
            'department_id'      => $departmentId
        ]);
    }

    /**
     * Update class-subject mapping (STRICT RULES)
     * Only safe fields allowed
     */
    public function updateMapping(int $id, array $data): bool
    {
        unset(
            $data['report_subject_id'],
            $data['class_id'],
            $data['school_id']
        );

        return $this->update($id, $data);
    }

    /**
     * Soft delete mapping
     */
    public function removeFromClass(int $id): bool
    {
        return $this->softDelete($id);
    }

    /**
     * Get single mapping (safe fetch)
     */
    public function findMapping(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT *
             FROM {$this->table}
             WHERE id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)",
            [$id]
        );
    }

    /**
     * Check if subject already assigned to class
     * (prevents duplicates)
     */
    public function exists(int $classId, int $subjectId): bool
    {
        $row = $this->db->fetch(
            "SELECT id
             FROM {$this->table}
             WHERE class_id = ?
             AND report_subject_id = ?
             AND (is_deleted = 0 OR is_deleted IS NULL)
             LIMIT 1",
            [$classId, $subjectId]
        );

        return $row !== false;
    }
}
