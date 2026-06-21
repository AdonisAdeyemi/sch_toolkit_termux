<?php

namespace ReportCard\Controllers;

use ReportCard\Models\CardPreferencesModel;
use Core\Controllers\BaseController;
use PDO;

class CardPreferencesController extends BaseController
{
    private CardPreferencesModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new CardPreferencesModel($pdo);
    }

    public function index(): void
    {
        $schoolId = $_SESSION['school_id'];

        $prefs = $this->model->getCardPreferences($schoolId);

        $this->render('card_preferences/index', [
        'title' => "Card Design Preferences",
        'appName' => $this->appName(),
            'prefs' => $prefs,
        ]);
    }

    public function save(): void
    {
        header('Content-Type: application/json');

        try {

            $schoolId = $_SESSION['school_id'];

            $ok = $this->model->updateCardPreferences($schoolId, $_POST);

            echo json_encode([
                'status' => $ok ? 'success' : 'error'
            ]);

        } catch (\Throwable $e) {

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
