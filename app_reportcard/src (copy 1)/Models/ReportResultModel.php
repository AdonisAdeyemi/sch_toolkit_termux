<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;

class ReportResultModel extends BaseModel
{
    protected string $table = 'report_results';


    
    public function existsForClassSubject(
    int $classSubjectId
): bool
{
    $stmt = $this->pdo->prepare(
        "SELECT 1
         FROM report_results
         WHERE class_subject_id = ?
         LIMIT 1"
    );

    $stmt->execute([$classSubjectId]);

    return (bool)$stmt->fetchColumn();
}
    
    
}










