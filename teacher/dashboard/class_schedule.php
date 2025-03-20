<?php
$page_title = "Class Schedule - Datamex College of Saint Adeline";
include_once('../../database/conn.php'); // Database connection
include('../../layouts/header.php'); // Header layout

// Start session and check if teacher is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
    header("Location: ../../login.php");
    exit();
}

// Get teacher's ID based on user_id
$user_id = $_SESSION['user_id'];
$teacher_query = "SELECT teacher_id, first_name, last_name FROM teachers WHERE user_id = ?";
$teacher_stmt = $conn->prepare($teacher_query);
$teacher_stmt->bind_param("i", $user_id);
$teacher_stmt->execute();
$teacher_result = $teacher_stmt->get_result();
$teacher = $teacher_result->fetch_assoc();
$teacher_stmt->close();

if (!$teacher) {
    echo "<p class='error-message'>Teacher profile not found.</p>";
    $conn->close();
    include('../../layouts/footer.php');
    exit();
}

$teacher_id = $teacher['teacher_id'];

// Fetch teacher's schedule
$schedule_query = "
    SELECT s.section_name, c.course_name, r.room_name, sch.day_of_week, sch.start_time, sch.end_time
    FROM teacher_sections ts
    JOIN sections s ON ts.section_id = s.section_id
    JOIN schedules sch ON s.section_id = sch.section_id
    JOIN enrollments e ON s.section_id = e.section_id
    JOIN courses c ON e.course_id = c.course_id
    JOIN rooms r ON e.room_id = r.room_id
    WHERE ts.teacher_id = ?
    GROUP BY s.section_id, sch.schedule_id
    ORDER BY FIELD(sch.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), sch.start_time";
$schedule_stmt = $conn->prepare($schedule_query);
$schedule_stmt->bind_param("i", $teacher_id);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();
?>

<h2 class="page-header-title">My Class Schedule</h2>

<?php if ($schedule_result->num_rows > 0): ?>
    <table id="myTable">
        <thead>
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Course</th>
                <th>Section</th>
                <th>Room</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $schedule_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['day_of_week']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['section_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No scheduled classes found for this week.</p>
<?php endif; ?>

<?php
    $schedule_stmt->close();
    $conn->close();
    include('../../layouts/footer.php'); // Footer layout
?>