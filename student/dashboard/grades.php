<?php
include('../../database/conn.php'); // Adjust path
$page_title = "My Grades - Datamex College of Saint Adeline";

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student_id
$student_sql = "SELECT student_id FROM students WHERE user_id = ?";
$student_stmt = mysqli_prepare($conn, $student_sql);
mysqli_stmt_bind_param($student_stmt, "i", $user_id);
mysqli_stmt_execute($student_stmt);
$student_result = mysqli_stmt_get_result($student_stmt);

if ($student_row = mysqli_fetch_assoc($student_result)) {
    $student_id = $student_row['student_id'];

    // Fetch grades for enrolled courses
    $grades_sql = "SELECT c.course_code, c.course_name, s.section_name,
                          g.prelim, g.midterm, g.pre_final, g.finals, g.average, g.remarks
                   FROM enrollments e
                   INNER JOIN courses c ON e.course_id = c.course_id
                   INNER JOIN sections s ON e.section_id = s.section_id
                   LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
                   WHERE e.student_id = ? AND e.status = 'active'
                   ORDER BY c.course_code";
    $grades_stmt = mysqli_prepare($conn, $grades_sql);
    mysqli_stmt_bind_param($grades_stmt, "i", $student_id);
    mysqli_stmt_execute($grades_stmt);
    $grades_result = mysqli_stmt_get_result($grades_stmt);
    $grades = mysqli_fetch_all($grades_result, MYSQLI_ASSOC);
} else {
    $grades = []; // No student found
}

?>

<?php include('../../layouts/header.php'); ?>

    <h2>My Grades</h2><hr><br>

    <?php if (!empty($grades)) { ?>
        <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Prelim</th>
                    <th>Midterm</th>
                    <th>Pre-Final</th>
                    <th>Finals</th>
                    <th>Average</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['course_code'] . " - " . $grade['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($grade['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($grade['prelim'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($grade['midterm'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($grade['pre_final'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($grade['finals'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($grade['average'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($grade['remarks'] ?? 'N/A'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No grades available for active courses.</p>
    <?php } ?>

<?php include('../../layouts/footer.php'); ?>