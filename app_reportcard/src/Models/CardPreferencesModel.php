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
    //prevent empty data ; implode [] cause sql error
    if (empty($data)) {
    return true;
}

/*
whitelist field - cos this method USESdynamic sql ie. just auto extracts & uploads any field in $_POST
*/
$allowed = [
    'printed_name',
    'address',
    'telephone',
    'primary_color_accent',
    'secondary_color_accent',
    'logo_url',
    'logo_watermark'
];

$data = array_intersect_key(
    $data,
    array_flip($allowed)
);

    
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










