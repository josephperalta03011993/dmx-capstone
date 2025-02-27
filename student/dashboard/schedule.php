<?php
include('../../database/conn.php'); // Adjust path
$page_title = "My Schedule - Datamex College of Saint Adeline";

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

    // Fetch schedule for enrolled courses
    $schedule_sql = "SELECT c.course_code, c.course_name, s.section_name, 
                            sch.day_of_week, sch.start_time, sch.end_time, 
                            r.room_name
                     FROM enrollments e
                     INNER JOIN sections s ON e.section_id = s.section_id
                     INNER JOIN courses c ON e.course_id = c.course_id
                     INNER JOIN schedules sch ON e.section_id = sch.section_id
                     LEFT JOIN rooms r ON e.room_id = r.room_id
                     WHERE e.student_id = ? AND e.status = 'active'
                     ORDER BY sch.day_of_week, sch.start_time";
    $schedule_stmt = mysqli_prepare($conn, $schedule_sql);
    mysqli_stmt_bind_param($schedule_stmt, "i", $student_id);
    mysqli_stmt_execute($schedule_stmt);
    $schedule_result = mysqli_stmt_get_result($schedule_stmt);
    $schedules = mysqli_fetch_all($schedule_result, MYSQLI_ASSOC);
} else {
    $schedules = []; // No student found
}

?>

<?php include('../../layouts/header.php'); ?>

    <h2>My Schedule</h2><hr><br>

    <?php if (!empty($schedules)) { ?>
        <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Room</th>
                    <th type="hidden"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['course_code'] . " - " . $schedule['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['start_time'] . " - " . $schedule['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['room_name'] ?? 'TBA'); ?></td>
                        <td type="hidden"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No scheduled courses found.</p>
    <?php } ?>

<?php include('../../layouts/footer.php'); ?>