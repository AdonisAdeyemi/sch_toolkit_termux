<?php

class DepartmentModel extends BaseModel
{
    protected string $table = 'departments';

    public function getClasses(int $departmentId): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM classes
             WHERE department_id = ?",
            [$departmentId]
        );
    }
}
