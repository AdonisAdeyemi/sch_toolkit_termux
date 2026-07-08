<?php
namespace ReportCard\Controllers;

use Core\Controllers\BaseController;
use ReportCard\Models\DashboardModel;
use ReportCard\Models\SchoolPeriodSettingsModel;

use PDO;
use Exception;





class DashboardController extends BaseController {

    private $pdo;
    private DashboardModel $dashboardModel;
 private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    $this->dashboardModel = new DashboardModel($pdo);
    $this->schoolPeriodSettingsModel = new SchoolPeriodSettingsModel($pdo);
    }


public function show()
{
    try {

        $title = $this->appName() . " Dashboard";
        $appName = $this->appName();

        $schoolId = $_SESSION['school_id'];

        $stats = $this->dashboardModel
            ->getDashboardStats($schoolId);
            
     $activePeriod = $this->schoolPeriodSettingsModel->getActivePeriod($schoolId) ?? [];       
            
            

        $this->render(
            'dashboard/dashboard',
            compact(
                'title',
                'appName',
                'stats',
                'activePeriod'
            )
        );

    } catch (\Throwable $e) {

        $errMsg = $e->getMessage();

        error_log("Dashboard error: " . $errMsg);

        setFlash(
            "danger",
            "Dashboard Error: " . $errMsg
        );

        log_debug($errMsg, "dashErr");
    }
}
}

?>








