<?php

class StudentModel extends BaseModel
{
    protected string $table = 'students';

    public function getByClass(int $classId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM students
             WHERE class_id = ?
             ORDER BY surname, first_name",
            [$classId]
        );
    }

    public function getByDepartment(int $departmentId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM students
             WHERE department_id = ?",
            [$departmentId]
        );
    }
}
