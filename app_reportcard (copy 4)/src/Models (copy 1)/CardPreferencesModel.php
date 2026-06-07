<?php
namespace ReportCard\Models;

use PDO;

class CardPreferencesModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
      //  $this->schoolId = $schoolId;
    }

    /**
     * Fetch full report card settings for a school
     */
    public function getCardPreferences($schoolId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM report_card_preferences
            WHERE school_id = :school_id
            LIMIT 1
        ");

        $stmt->execute([
            ':school_id' => $schoolId
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Optional: update settings (useful later)
     */
    public function updateCardPreferences($schoolId, array $data): bool
    {
        // Build dynamic SQL safely
        $fields = [];
        $params = [':school_id' => $schoolId];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $sql = "
            UPDATE report_card_preferences
            SET " . implode(", ", $fields) . "
            WHERE school_id = :school_id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }
}










