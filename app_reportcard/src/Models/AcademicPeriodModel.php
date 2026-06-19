<?php
namespace ReportCard\Models;


use Core\Models\BaseModel;
use PDO;

class AcademicPeriodModel  extends BaseModel
{
protected string $table = 'report_academic_periods';


public function getPeriodsList(): array
{
    $stmt = $this->pdo->query("
        SELECT
            id,
            CONCAT(session, ' - Term ', term) AS period_name
        FROM report_academic_periods
        ORDER BY id DESC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}













}
 
 
 
 
 
 
