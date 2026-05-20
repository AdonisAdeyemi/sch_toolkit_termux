<?php

//Handles class/session/settings:

class MetaModel {

    public static function getClassName($pdo, $class_id) {
        return $pdo->query("SELECT class_name FROM report_classes WHERE id = $class_id")
                   ->fetchColumn();
    }

    public static function getPeriod($pdo, $period_id) {
        return $pdo->query("SELECT session, term FROM report_academic_periods WHERE id = $period_id")
                   ->fetch(PDO::FETCH_ASSOC);
    }

    public static function getSettings($pdo) {
        return $pdo->query("SELECT * FROM report_card_settings WHERE school_id = 1")
                   ->fetch(PDO::FETCH_ASSOC);
    }
}

?>
