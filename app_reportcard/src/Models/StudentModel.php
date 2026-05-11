class StudentModel
{
    public function getReportData($pdo, $class_id, $period_id)
    {
        // put DB logic here OR delegate to query methods
        $stmt = $pdo->prepare("
            SELECT * FROM students WHERE class_id = ?
        ");

        $stmt->execute([$class_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
