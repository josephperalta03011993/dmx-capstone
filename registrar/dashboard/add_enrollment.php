<?php
$page_title = "Add Enrollment";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

$success = null;
$error = null;

// Fetch data for dropdowns
$students = $conn->query("SELECT student_id, first_name, last_name FROM students")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT section_id, section_name FROM sections")->fetch_all(MYSQLI_ASSOC);
$courses = $conn->query("SELECT course_id, course_name FROM courses")->fetch_all(MYSQLI_ASSOC);
$rooms = $conn->query("SELECT room_id, room_name FROM rooms")->fetch_all(MYSQLI_ASSOC);

// Save enrollment request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_enrollment"])) {
    $student_id = sanitize_input($conn, $_POST["student_id"]);
    $section_id = sanitize_input($conn, $_POST["section_id"]);
    $course_id = sanitize_input($conn, $_POST["course_id"]);
    $room_id = sanitize_input($conn, $_POST["room_id"]);
    $enrollment_date = sanitize_input($conn, $_POST["enrollment_date"]);
    $status = sanitize_input($conn, $_POST['status']);

    $overlap = false;
    $schedule_sql = "SELECT day_of_week, start_time, end_time FROM schedules WHERE section_id = ?";
    $schedule_stmt = mysqli_prepare($conn, $schedule_sql);
    mysqli_stmt_bind_param($schedule_stmt, "i", $section_id);
    mysqli_stmt_execute($schedule_stmt);
    $schedule_result = mysqli_stmt_get_result($schedule_stmt);

    while ($schedule_row = mysqli_fetch_assoc($schedule_result)) {
        $overlap_sql = "SELECT s.start_time, s.end_time FROM schedules s
                        JOIN enrollments e ON s.section_id = e.section_id
                        WHERE e.student_id = ? AND s.day_of_week = ?";
        $overlap_stmt = mysqli_prepare($conn, $overlap_sql);
        mysqli_stmt_bind_param($overlap_stmt, "is", $student_id, $schedule_row['day_of_week']);
        mysqli_stmt_execute($overlap_stmt);
        $overlap_result = mysqli_stmt_get_result($overlap_stmt);

        if($overlap_result->num_rows > 0){
            $new_start_time = $schedule_row['start_time'];
            $new_end_time = $schedule_row['end_time'];

            while ($row = mysqli_fetch_assoc($overlap_result)) {
                if (($new_start_time >= $row['start_time'] && $new_start_time < $row['end_time']) ||
                    ($new_end_time > $row['start_time'] && $new_end_time <= $row['end_time'])) {
                    $overlap = true;
                    break 2; // Break out of both loops
                }
            }
        }
    }

    if ($overlap) {
        $error = "Time overlap detected. Student already has a class scheduled at that time on the same day.";
    } else {
        $sql = "INSERT INTO enrollments (student_id, section_id, course_id, room_id, enrollment_date, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Bind the parameters
            mysqli_stmt_bind_param($stmt, "iiiiss", $student_id, $section_id, $course_id, $room_id, $enrollment_date, $status);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                $success = "Enrollment created successfully!";

                // Update student status to 'enrolled'
                $update_student_sql = "UPDATE students SET status = 'enrolled' WHERE student_id = ?";
                $update_student_stmt = mysqli_prepare($conn, $update_student_sql);

                if ($update_student_stmt) {
                    mysqli_stmt_bind_param($update_student_stmt, "i", $student_id);
                    if (!mysqli_stmt_execute($update_student_stmt)) {
                        $error = "Enrollment created successfully, but error updating student status: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($update_student_stmt);
                } else {
                    $error = "Enrollment created successfully, but error preparing student status update: " . mysqli_error($conn);
                }

            } else {
                $error = "Error creating enrollment: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Error preparing enrollment statement: " . mysqli_error($conn);
        }
    }
}

?>

<h2>Add Enrollment</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="section-form-container">
        <div class="one-col-form-group">
            <label for="student_id">Student:</label>
            <select name="student_id" id="student_id" required>
                <?php foreach ($students as $student) { ?>
                    <option value="<?php echo $student['student_id']; ?>"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></option>
                <?php } ?>
            </select><br>
        </div>
        <div class="one-col-form-group">
            <label for="section_id">Section:</label>
            <select name="section_id" id="section_id" required>
                <?php foreach ($sections as $section) { ?>
                    <option value="<?php echo $section['section_id']; ?>"><?php echo $section['section_name']; ?></option>
                <?php } ?>
            </select><br>
        </div>
        <div class="one-col-form-group">
            <label for="course_id">Course:</label>
            <select name="course_id" id="course_id" required>
                <?php foreach ($courses as $course) { ?>
                    <option value="<?php echo $course['course_id']; ?>"><?php echo $course['course_name']; ?></option>
                <?php } ?>
            </select><br>
        </div>
        <div class="one-col-form-group">
            <label for="room_id">Room:</label>
            <select name="room_id" id="room_id" required>
                <?php foreach ($rooms as $room) { ?>
                    <option value="<?php echo $room['room_id']; ?>"><?php echo $room['room_name']; ?></option>
                <?php } ?>
            </select><br>
        </div>
        <div class="one-col-form-group">
            <label for="enrollment_date">Enrollment Date:</label>
            <input type="date" 
                id="enrollment_date" 
                name="enrollment_date" 
                value="<?php echo date('Y-m-d'); ?>" 
                max="<?php echo date('Y-m-d'); ?>" 
                required>
            <br>
        </div>
        <div class="one-col-form-group">
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="active">Active</option>
                <option value="dropped">Dropped</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>
    <button type="submit" name="create_enrollment" id="create_enrollment" class="btn_submit">
        <i class="fa-solid fa-floppy-disk"></i> Admit Student
    </button>
</form>

<?php include('../../layouts/footer.php'); ?>