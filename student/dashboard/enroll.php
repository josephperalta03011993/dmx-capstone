<?php
include('../../database/conn.php'); // Adjust path
$page_title = "Enroll in Courses - Datamex College of Saint Adeline";

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

if (!$student_row = mysqli_fetch_assoc($student_result)) {
    die("Student not found.");
}
$student_id = $student_row['student_id'];

// Check payment status
$payment_sql = "SELECT COUNT(*) as unpaid FROM payments WHERE student_id = ? AND payment_status IN ('Pending', 'Failed')";
$payment_stmt = mysqli_prepare($conn, $payment_sql);
mysqli_stmt_bind_param($payment_stmt, "i", $student_id);
mysqli_stmt_execute($payment_stmt);
$payment_result = mysqli_stmt_get_result($payment_stmt);
$payment_row = mysqli_fetch_assoc($payment_result);
$can_enroll = ($payment_row['unpaid'] == 0);

// Check grades for 'INC'
$grade_sql = "SELECT COUNT(*) as inc_grades FROM grades WHERE enrollment_id IN 
              (SELECT enrollment_id FROM enrollments WHERE student_id = ?) AND remarks = 'INC'";
$grade_stmt = mysqli_prepare($conn, $grade_sql);
mysqli_stmt_bind_param($grade_stmt, "i", $student_id);
mysqli_stmt_execute($grade_stmt);
$grade_result = mysqli_stmt_get_result($grade_stmt);
$grade_row = mysqli_fetch_assoc($grade_result);
$can_enroll = $can_enroll && ($grade_row['inc_grades'] == 0);

// Handle enrollment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_enroll) {
    $section_id = $_POST['section_id'];
    $course_id = $_POST['course_id'];

    // Fetch room_id from sections
    $room_sql = "SELECT room_id FROM sections WHERE section_id = ?";
    $room_stmt = mysqli_prepare($conn, $room_sql);
    mysqli_stmt_bind_param($room_stmt, "i", $section_id);
    mysqli_stmt_execute($room_stmt);
    $room_result = mysqli_stmt_get_result($room_stmt);
    $room_row = mysqli_fetch_assoc($room_result);
    $room_id = $room_row['room_id'] ?: NULL;

    // Validate course_id exists
    $validate_sql = "SELECT COUNT(*) FROM courses WHERE course_id = ?";
    $validate_stmt = mysqli_prepare($conn, $validate_sql);
    mysqli_stmt_bind_param($validate_stmt, "i", $course_id);
    mysqli_stmt_execute($validate_stmt);
    $validate_result = mysqli_stmt_get_result($validate_stmt);
    $course_exists = mysqli_fetch_assoc($validate_result)['COUNT(*)'] > 0;

    if (!$course_exists) {
        $error = "Invalid course selected. Please try again.";
    } else {
        $enroll_sql = "INSERT INTO enrollments (student_id, section_id, course_id, room_id, enrollment_date, status) 
                       VALUES (?, ?, ?, ?, CURDATE(), 'active')";
        $enroll_stmt = mysqli_prepare($conn, $enroll_sql);
        mysqli_stmt_bind_param($enroll_stmt, "iiii", $student_id, $section_id, $course_id, $room_id);
        if (mysqli_stmt_execute($enroll_stmt)) {
            $success = "Successfully enrolled!";
        } else {
            $error = "Enrollment failed: " . mysqli_error($conn);
        }
    }
}

// Fetch available sections with schedule info
$sections_sql = "SELECT s.section_id, s.section_name, c.course_id, c.course_code, c.course_name, 
                        s.room_id, r.room_name, sch.day_of_week, sch.start_time, sch.end_time
                 FROM sections s
                 INNER JOIN courses c ON s.course_id = c.course_id
                 LEFT JOIN rooms r ON s.room_id = r.room_id
                 LEFT JOIN schedules sch ON s.section_id = sch.section_id
                 WHERE s.section_id NOT IN (
                     SELECT section_id FROM enrollments WHERE student_id = ? AND status = 'active'
                 )";
$sections_stmt = mysqli_prepare($conn, $sections_sql);
mysqli_stmt_bind_param($sections_stmt, "i", $student_id);
mysqli_stmt_execute($sections_stmt);
$sections_result = mysqli_stmt_get_result($sections_stmt);
$available_sections = mysqli_fetch_all($sections_result, MYSQLI_ASSOC);

// Group schedules by section
$sections_with_schedules = [];
foreach ($available_sections as $section) {
    $section_id = $section['section_id'];
    if (!isset($sections_with_schedules[$section_id])) {
        $sections_with_schedules[$section_id] = [
            'section_id' => $section['section_id'],
            'section_name' => $section['section_name'],
            'course_id' => $section['course_id'],
            'course_code' => $section['course_code'],
            'course_name' => $section['course_name'],
            'room_id' => $section['room_id'],
            'room_name' => $section['room_name'],
            'schedules' => []
        ];
    }
    if ($section['day_of_week']) {
        $sections_with_schedules[$section_id]['schedules'][] = [
            'day_of_week' => $section['day_of_week'],
            'start_time' => $section['start_time'],
            'end_time' => $section['end_time']
        ];
    }
}
$available_sections = array_values($sections_with_schedules);

?>

<?php include('../../layouts/header.php'); ?>

    <h2>Enroll in Courses</h2><hr><br>

    <?php if (isset($success)) { ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php } elseif (isset($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>

    <?php if (!$can_enroll) { ?>
        <p style="color: red;">You cannot enroll due to unpaid fees or incomplete grades (INC).</p>
    <?php } else { ?>
        <?php if (!empty($available_sections)) { ?>
            <form method="POST">
                <label for="section_id">Select a Section to Enroll:</label>
                <select name="section_id" id="section_id" required>
                    <option value="" disabled selected>Choose a course</option>
                    <?php foreach ($available_sections as $section) { ?>
                        <option value="<?php echo $section['section_id']; ?>" 
                                data-course-id="<?php echo $section['course_id']; ?>">
                            <?php 
                            $schedule_text = '';
                            foreach ($section['schedules'] as $sched) {
                                $schedule_text .= " {$sched['day_of_week']} {$sched['start_time']}-{$sched['end_time']},";
                            }
                            $schedule_text = rtrim($schedule_text, ',');
                            echo htmlspecialchars($section['course_code'] . " - " . $section['course_name'] . 
                                " (" . $section['section_name'] . ")" . 
                                ($schedule_text ? " [$schedule_text]" : "") . 
                                " [" . ($section['room_name'] ?? 'TBA') . "]");
                            ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="hidden" name="course_id" id="course_id">
                <button type="submit" class="btn-enrollment">Enroll</button>
            </form>

            <script>
                document.getElementById('section_id').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    document.getElementById('course_id').value = selectedOption.getAttribute('data-course-id');
                });
            </script>
        <?php } else { ?>
            <p>No available sections to enroll in.</p>
        <?php } ?>
    <?php } ?>

<?php include('../../layouts/footer.php'); ?>