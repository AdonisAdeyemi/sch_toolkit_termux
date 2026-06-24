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
    
    var_dump ($_SESSION);

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
    
        header('Content-Type: application/json');
        
        try{
        $schoolId = $_SESSION['school_id'];

        $periodId = (int) ($_POST['period_id'] ?? 0);
        
 
/*******************
 CHECK LOCK STATUS 
 ******************/

$isAdmin = ($_SESSION['role'] ?? '') === 'admin'
||
 ($_SESSION['role'] ?? '') === 'creator'
;

$error = $this->canEditPeriod(
    $schoolId,
    $periodId,
    $isAdmin
);

if ($error) {

    echo json_encode([
        'status' => 'error',
        'message' => $error
    ]);

    return;
}

/**********/



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
    catch (\Throwable $e) {

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    
}

/*
    public function toggleLock()
    {
        header('Content-Type: application/json');

        $schoolId = $_SESSION['school_id'] ?? null;
        $periodId = $_POST['period_id'] ?? null;
      $lock_status =  

        if (!$schoolId || !$periodId) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing parameters'
            ]);
            return;
        }

        $this->schoolPeriodSettingsModel->toggleLock($schoolId, (int)$periodId);

        $newStatus = $this->schoolPeriodSettingsModel->getLockStatus($schoolId, (int)$periodId);

        echo json_encode([
            'status' => 'success',
            'is_locked' => $newStatus,
            'message' => $newStatus ? 'Locked successfully' : 'Unlocked successfully'
        ]);
    }
*/

public function updateLockStatus()
{
    header('Content-Type: application/json');

    $schoolId   = $_SESSION['school_id'] ?? null;
    $periodId   = (int) ($_POST['period_id'] ?? 0);
    $lockStatus = (int) ($_POST['lock_status'] ?? -1);

    if (
        !$schoolId ||
        !$periodId ||
        !in_array($lockStatus, [0, 1, 2], true)
    ) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Invalid parameters'
        ]);
        return;
    }

    $success = $this->schoolPeriodSettingsModel
        ->updateLockStatus(
            $schoolId,
            $periodId,
            $lockStatus
        );

    if (!$success) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Unable to update status'
        ]);
        return;
    }

  $lockStatusConfirmed = $this ->schoolPeriodSettingsModel
        -> getLockStatus( $schoolId, $periodId) ;


    echo json_encode([
        'status'      => 'success',
        'lock_status' => $lockStatusConfirmed,
        'message'     => match ($lockStatus) {
            0 => 'Period opened successfully',
            1 => 'Teacher lock enabled',
            2 => 'Permanent lock enabled'
        }
    ]);
}





}









