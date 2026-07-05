<?php
namespace ReportCard\Models;


use Core\Models\BaseModel;
use PDO;

class AcademicPeriodModel  extends BaseModel
{
protected string $table = 'report_academic_periods';


public function getPeriodsList(): array
{
/*
    $stmt = $this->pdo->query("
        SELECT
            id,
            CONCAT(session, ' - Term ', term) AS period_name
        FROM report_academic_periods
        ORDER BY id DESC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    */
    
    
    
        $stmt = $this->pdo->query("
    SELECT
    p.id,
  CONCAT( s.session_name, ' - Term ', p.term) AS period_name
FROM report_academic_periods p
JOIN report_academic_sessions s
    ON s.id = p.session_id
        ORDER BY id DESC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/****************/


public function getPeriodRowFromPeriodId(int $periodId): array
{

    $sql = "
    SELECT
    p.*,
  CONCAT( s.session_name, ' - Term ', p.term) AS period_name
FROM report_academic_periods p
JOIN report_academic_sessions s
    ON s.id = p.session_id
WHERE p.id = ?
LIMIT 1
        
    " ;

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$periodId]);


    return $stmt->fetch(PDO::FETCH_ASSOC);
}



/*******************/

public function getSessionIdByPeriodId(int $periodId): ?int
{
    $sql = "
        SELECT session_id
        FROM report_academic_periods
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$periodId]);

    $sessionId = $stmt->fetchColumn();

    return $sessionId ? (int)$sessionId : null;
}










}
 
 
 
 
 
 
