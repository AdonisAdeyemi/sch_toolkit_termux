<?php

namespace ReportCard\Controllers;

use ReportCard\Models\SchoolPeriodSettingsModel;
use ReportCard\Models\AcademicPeriodModel;
use Core\Controllers\BaseController;
use PDO;

class SchoolPeriodSettingsController extends BaseController
{
    private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;
 private AcademicPeriodModel $academicPeriodModel;
    private $pdo;

    public function __construct(PDO $pdo)
    {
    $this->pdo = $pdo;
        $this->schoolPeriodSettingsModel = new SchoolPeriodSettingsModel($pdo);
          $this->academicPeriodModel = new AcademicPeriodModel($pdo);
    }

    public function index(): void
    {
        $schoolId = $_SESSION['school_id'];

        $periodId = $_GET['period_id'] ?? null;


  $periods = $this->academicPeriodModel->getPeriodsList();

        $settings = null;

        if ($periodId) {
            $settings = $this->schoolPeriodSettingsModel->getBySchoolAndPeriod(
                $schoolId,
                (int)$periodId
            );
        }
      
   
    $appName = $this->appName();
    $title = "School Settings";
    
    var_dump ("settings : ", $settings ) ;

        $this->render('school_settings/index', [
        'appName' => $appName ,
        'title' => $title,
            'periods' => $periods,
            'periodId' => $periodId,
            'settings' => $settings
        ]);
    }

    public function save(): void
    {
        $schoolId = $_SESSION['school_id'];

        $periodId = (int) ($_POST['period_id'] ?? 0);

        if (!$periodId) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Period required'
            ]);
            return;
        }

        $ok = $this->schoolPeriodSettingsModel->upsert(
            $schoolId,
            $periodId,
            $_POST
        );

        echo json_encode([
            'status' => $ok ? 'success' : 'error'
        ]);
    }
}
