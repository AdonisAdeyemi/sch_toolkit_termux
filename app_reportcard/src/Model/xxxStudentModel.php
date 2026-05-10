<?php
namespace ReportCard\Model;

use ReportCard\Core\ReportBuilder;


class StudentModel {
    public function getReportData($pdo, $class_id, $period_id) {
    
        $builder = new ReportBuilder($pdo);
        return $builder->build($class_id, $period_id);
    }
}

?>
