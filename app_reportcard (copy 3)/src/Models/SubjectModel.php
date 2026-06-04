<?php


class SubjectModel extends BaseModel
{
    protected string $table = 'subjects';

    public function getByDepartment(int $departmentId): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM subjects
             WHERE department_id = ?",
            [$departmentId]
        );
    }
}
