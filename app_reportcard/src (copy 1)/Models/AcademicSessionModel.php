<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;

class AcademicSessionModel extends BaseModel
{

    /**
     * Get all academic sessions
     */
    public function getAllSessions(): array
    {
        return $this->fetchAll(
            "
            SELECT
                id,
                session_name
            FROM report_academic_sessions
            ORDER BY id DESC
            "
        );
    }

    /**********************************/

    /**
     * Get session by ID
     */
    public function getSessionById(
        int $sessionId
    ): ?array {

        return $this->fetch(
            "
            SELECT *
            FROM report_academic_sessions
            WHERE id = ?
            ",
            [$sessionId]
        );
    }

    /**********************************/

    /**
     * Get session name
     */
    public function getSessionNameById(
        int $sessionId
    ): ?string {

        $sql = "
            SELECT session_name
            FROM report_academic_sessions
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sessionId]);

        $name = $stmt->fetchColumn();

        return $name ?: null;
    }

    /**********************************/

    /**
     * Create a new academic session
     */
    public function createSession(
        string $sessionName
    ): bool {

        $sql = "
            INSERT INTO report_academic_sessions
            (
                session_name
            )
            VALUES
            (
                ?
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $sessionName
        ]);
    }

    /**********************************/

    /**
     * Update session name
     */
    public function updateSession(
        int $sessionId,
        string $sessionName
    ): bool {

        $sql = "
            UPDATE report_academic_sessions
            SET session_name = ?
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $sessionName,
            $sessionId
        ]);
    }

    /**********************************/

    /**
     * Delete session
     */
    public function deleteSession(
        int $sessionId
    ): bool {

        $sql = "
            DELETE
            FROM report_academic_sessions
            WHERE id = ?
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $sessionId
        ]);
    }

}
