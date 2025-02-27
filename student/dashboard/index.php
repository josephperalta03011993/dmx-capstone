<?php
include('../../database/conn.php'); // Adjust path to conn.php
$page_title = "Student Dashboard - Datamex College of Saint Adeline";

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../../login.php"); // Redirect to login if not a student
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student_id from students table
$student_sql = "SELECT student_id FROM students WHERE user_id = ?";
$student_stmt = mysqli_prepare($conn, $student_sql);
mysqli_stmt_bind_param($student_stmt, "i", $user_id);
mysqli_stmt_execute($student_stmt);
$student_result = mysqli_stmt_get_result($student_stmt);

if ($student_row = mysqli_fetch_assoc($student_result)) {
    $student_id = $student_row['student_id'];

    // Fetch enrolled courses
    $enrollment_sql = "SELECT e.enrollment_id, e.course_id, e.section_id, e.status, e.enrollment_date,
                             c.course_code, c.course_name, s.section_name
                       FROM enrollments e
                       INNER JOIN courses c ON e.course_id = c.course_id
                       INNER JOIN sections s ON e.section_id = s.section_id
                       WHERE e.student_id = ? AND e.status = 'active'";
    $enrollment_stmt = mysqli_prepare($conn, $enrollment_sql);
    mysqli_stmt_bind_param($enrollment_stmt, "i", $student_id);
    mysqli_stmt_execute($enrollment_stmt);
    $enrollment_result = mysqli_stmt_get_result($enrollment_stmt);
    $enrollments = mysqli_fetch_all($enrollment_result, MYSQLI_ASSOC);
} else {
    $enrollments = []; // No student found
}

?>

<?php include('../../layouts/header.php'); ?>

    <h2>Student Dashboard</h2><hr><br>

    <div id="container">
        <?php if (!empty($enrollments)) { ?>
            <?php foreach ($enrollments as $enrollment) { ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($enrollment['course_code'] . " - " . $enrollment['course_name']); ?></h3>
                    <p><strong>Section:</strong> <?php echo htmlspecialchars($enrollment['section_name']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($enrollment['status']); ?></p>
                    <p><strong>Enrolled On:</strong> <?php echo htmlspecialchars($enrollment['enrollment_date'] ?? 'N/A'); ?></p>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No enrolled courses found.</p>
        <?php } ?>
    </div>

<?php include('../../layouts/footer.php'); ?>