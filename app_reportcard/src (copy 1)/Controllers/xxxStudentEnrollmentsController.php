<?php

namespace ReportCard\Controllers;



use ReportCard\Models\EnrollmentModel;
use ReportCard\Models\AcademicSessionModel;
use ReportCard\Models\ClassModel;


class StudentEnrollmentsController extends BaseController
{
    private EnrollmentModel $enrollmentModel;
    private AcademicSessionModel $academicSessionModel;
    private ClassModel $classModel;

    public function __construct()
    {
        parent::__construct();

        $this->enrollmentModel = new EnrollmentModel();
        $this->academicSessionModel = new AcademicSessionModel();
        $this->classModel = new ClassModel();
    }

    public function index()
    {
        $schoolId = $_SESSION['school_id'];

        $sessionId = (int)($_GET['session_id'] ?? 0);
        $classId   = (int)($_GET['class_id'] ?? 0);

        $sessions = $this->academicSessionModel->getAllSessions();
        $classes  = $this->classModel->getClasses($schoolId);

        $students = [];

        if ($sessionId) {

            if ($classId) {

                $students =
                    $this->enrollmentModel
                        ->getStudentsInClass(
                            $schoolId,
                            $sessionId,
                            $classId
                        );

            } else {

                $students =
                    $this->enrollmentModel
                        ->getStudentsInSession(
                            $schoolId,
                            $sessionId
                        );

            }

        }

        $this->view(
            'student_enrollments/index',
            compact(
                'sessions',
                'classes',
                'students',
                'sessionId',
                'classId'
            )
        );
    }
}
