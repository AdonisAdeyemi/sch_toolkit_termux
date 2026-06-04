<?php

class ClassSubjectModel extends BaseModel
{
    protected string $table = 'class_subjects';

    public function getSubjectsByClass(int $classId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*
             FROM class_subjects cs
             JOIN subjects s ON s.id = cs.subject_id
             WHERE cs.class_id = ?",
            [$classId]
        );
    }

    public function getClassesBySubject(int $subjectId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*
             FROM class_subjects cs
             JOIN classes c ON c.id = cs.class_id
             WHERE cs.subject_id = ?",
            [$subjectId]
        );
    }
}
