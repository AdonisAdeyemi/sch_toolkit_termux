Yes. Let's keep the MVC clean. The controller should not perform SQL queries directly. Create a DashboardModel that returns all dashboard statistics.

DashboardController

public function show()
{
    try {

        $dashboardModel = new DashboardModel();

        $stats = $dashboardModel->getDashboardStats(
            $_SESSION['school_id']
        );

        $title = $this->appName() . " Dashboard";
        $appName = $this->appName();

        extract($stats);

        $this->render(
            'dashboard/dashboard',
            compact(
                'title',
                'appName',

                'totalStudents',
                'totalClasses',
                'totalSubjects',

                'currentSessionName',
                'currentTermName',

                'resultsLockStatus',

                'studentsAwaitingEnrollment',

                'studentsWithIncompleteResults'
            )
        );

    } catch (\Throwable $e) {

        error_log($e->getMessage());

        setFlash(
            "danger",
            "Dashboard Error : " . $e->getMessage()
        );

        log_debug(
            $e->getMessage(),
            "dashErr"
        );
    }
}


---

DashboardModel

class DashboardModel extends BaseModel
{
    public function getDashboardStats(
        int $schoolId
    ): array
    {

        return [

            'totalStudents' => 0,

            'totalClasses' => 0,

            'totalSubjects' => 0,

            'currentSessionName' => '',

            'currentTermName' => '',

            'resultsLockStatus' => 0,

            'studentsAwaitingEnrollment' => 0,

            'studentsWithIncompleteResults' => 0

        ];

    }
}

We'll replace each 0 with real queries one by one.


---

My suggested implementation order

These are easiest first:

1

$totalStudents


---

2

$totalClasses


---

3

$totalSubjects


---

4

$currentSessionName
$currentTermName
$resultsLockStatus

These all come from your current period tables.


---

5

$studentsAwaitingEnrollment


---

6

$studentsWithIncompleteResults

This will be the largest query and should be done last.


--- 

I also recommend creating one method per statistic instead of putting everything into one large SQL query:

private function getTotalStudents($schoolId)

private function getTotalClasses($schoolId)

private function getTotalSubjects($schoolId)

private function getCurrentPeriodInfo($schoolId)

private function getStudentsAwaitingEnrollment($schoolId)
 
private function getStudentsWithIncompleteResults($schoolId)

Then getDashboardStats() simply combines them. This keeps the model much easier to read, test, and maintain as your application grows.









