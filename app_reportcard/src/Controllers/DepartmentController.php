<?php

namespace ReportCard\Controllers;

use ReportCard\Models\DepartmentModel;
use Core\Controllers\BaseController;
use PDO;

class DepartmentController extends BaseController
{
    private DepartmentModel $departmentModel;

    public function __construct(PDO $pdo)
    {
        $this->model = new DepartmentModel($pdo);
    }

public function getAllDepartments(): void
{
    $classId = (int) ($_GET['class_id'] ?? 0);

    if ($classId <= 0) {

        echo json_encode([]);

        return;
    }

    $departments =
        $this->departmentModel
            ->getAllDepartments(
                $classId
            );

    echo json_encode($departments);
}


}









