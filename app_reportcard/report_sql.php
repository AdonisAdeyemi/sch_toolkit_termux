<?php
$report_sql = " SELECT 
    s.id AS student_id,
    s.student_name,
    s.religion,

    c.id AS class_id,
    c.class_name,

    cp.id AS period_id,
    cp.session,
    cp.term,

    cs.id AS class_subject_id,
    cs.subject_order,
    cs.department_id,
    cs.alias_name,

    rs.id AS subject_id,
    rs.name AS subject_name,
    
    sbj.name AS base_subject_name,

    et.name AS exam_type,
    et.id AS exam_type_id,
    et.max_score,
    
    r.score,
    
    t_exam_comm.comment AS teacher_exam_comment,
    p_exam_comm.comment AS principal_exam_comment,
    
    att.days_present,
    ps.days_open

FROM report_students s

JOIN report_classes c 
    ON c.id = s.class_id

JOIN report_academic_periods cp 
    ON cp.id = ?

JOIN report_class_subjects cs 
    ON cs.class_id = c.id

JOIN report_subjects rs 
    ON rs.id = cs.report_subject_id

JOIN report_exam_types et ON 1=1


LEFT JOIN subjects sbj
    ON sbj.id = rs.base_subject_id

LEFT JOIN report_results r 
    ON r.student_id = s.id
    AND r.class_subject_id = cs.id
    AND r.period_id = cp.id
    AND r.exam_type_id = et.id

LEFT JOIN report_student_departments sd
    ON sd.student_id = s.id
    AND sd.period_id = cp.id
    
LEFT JOIN report_card_info t_exam_comm
    ON t_exam_comm.student_id = s.id
    AND t_exam_comm.period_id = cp.id
    AND t_exam_comm.comment_type = 'class_teacher'
    AND t_exam_comm.assessment_type = 'exam'
    
LEFT JOIN report_card_info p_exam_comm
    ON p_exam_comm.student_id = s.id
    AND p_exam_comm.period_id = cp.id
    AND p_exam_comm.comment_type = 'principal'
    AND p_exam_comm.assessment_type = 'exam'
    
LEFT JOIN report_attendance att
    ON att.student_id = s.id
    AND att.period_id = cp.id
    
LEFT JOIN report_period_settings ps
    ON ps.period_id = cp.id

WHERE c.id = ?

AND (
    cs.department_id IS NULL
    OR cs.department_id = sd.department_id
)

ORDER BY 
    s.id,
    cs.subject_order,
    et.id;
    "
    
?>
    
    
    
    
    
    
    
