<?php
$page_title = "Edit Enrollment";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

$success = null;
$error = null;

// Fetch data for dropdowns
$students = $conn->query("SELECT student_id, first_name, last_name FROM students")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT section_id, section_name FROM sections")->fetch_all(MYSQLI_ASSOC);
$courses = $conn->query("SELECT course_id, course_name FROM courses")->fetch_all(MYSQLI_ASSOC);
$rooms = $conn->query("SELECT room_id, room_name FROM rooms")->fetch_all(MYSQLI_ASSOC);

// Fetch enrollment data
if (isset($_GET['id'])) {
    $enrollment_id = sanitize_input($conn, $_GET['id']);
    $enrollment_sql = "SELECT student_id, section_id, course_id, room_id, enrollment_date, status FROM enrollments WHERE enrollment_id = ?";
    $enrollment_stmt = mysqli_prepare($conn, $enrollment_sql);
    mysqli_stmt_bind_param($enrollment_stmt, "i", $enrollment_id);
    mysqli_stmt_execute($enrollment_stmt);
    $enrollment_result = mysqli_stmt_get_result($enrollment_stmt);
    $enrollment = mysqli_fetch_assoc($enrollment_result);
}

// Update enrollment request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_enrollment"])) {
    $enrollment_id = sanitize_input($conn, $_POST["enrollment_id"]);
    $student_id = sanitize_input($conn, $_POST["student_id"]);
    $section_id = sanitize_input($conn, $_POST["section_id"]);
    $course_id = sanitize_input($conn, $_POST["course_id"]);
    $room_id = sanitize_input($conn, $_POST["room_id"]);
    $enrollment_date = sanitize_input($conn, $_POST["enrollment_date"]);
    $status = sanitize_input($conn, $_POST['status']);

    $sql = "UPDATE enrollments SET student_id = ?, section_id = ?, course_id = ?, room_id = ?, enrollment_date = ?, status = ? WHERE enrollment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiiissi", $student_id, $section_id, $course_id, $room_id, $enrollment_date, $status, $enrollment_id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Enrollment updated successfully!";
    } else {
        $error = "Error updating enrollment: " . mysqli_error($conn);
    }
}

?>

<h2>Edit Enrollment</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<?php if (isset($enrollment)) { ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $enrollment_id; ?>">
        <input type="hidden" name="enrollment_id" value="<?php echo $enrollment_id; ?>">
        <div class="section-form-container">
            <div class="one-col-form-group">
                <label for="student_id">Student:</label>
                <select name="student_id" id="student_id" required>
                    <?php foreach ($students as $student) { ?>
                        <option value="<?php echo $student['student_id']; ?>" <?php if ($student['student_id'] == $enrollment['student_id']) echo 'selected'; ?>><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></option>
                    <?php } ?>
                </select><br>
            </div>
            <div class="one-col-form-group">
                <label for="section_id">Section:</label>
                <select name="section_id" id="section_id" required>
                    <?php foreach ($sections as $section) { ?>
                        <option value="<?php echo $section['section_id']; ?>" <?php if ($section['section_id'] == $enrollment['section_id']) echo 'selected'; ?>><?php echo $section['section_name']; ?></option>
                    <?php } ?>
                </select><br>
            </div>
            <div class="one-col-form-group">
                <label for="course_id">Course:</label>
                <select name="course_id" id="course_id" required>
                    <?php foreach ($courses as $course) { ?>
                        <option value="<?php echo $course['course_id']; ?>" <?php if ($course['course_id'] == $enrollment['course_id']) echo 'selected'; ?>><?php echo $course['course_name']; ?></option>
                    <?php } ?>
                </select><br>
            </div>
            <div class="one-col-form-group">
                <label for="room_id">Room:</label>
                <select name="room_id" id="room_id" required>
                    <?php foreach ($rooms as $room) { ?>
                        <option value="<?php echo $room['room_id']; ?>" <?php if ($room['room_id'] == $enrollment['room_id']) echo 'selected'; ?>><?php echo $room['room_name']; ?></option>
                    <?php } ?>
                </select><br>
            </div>
            <div class="one-col-form-group">
                <label for="enrollment_date">Enrollment Date:</label>
                <input type="date" id="enrollment_date" name="enrollment_date" value="<?php echo $enrollment['enrollment_date']; ?>" required><br>
            </div>
            <div class="one-col-form-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="active" <?php if ($enrollment['status'] == 'active') echo 'selected'; ?>>Active</option>
                    <option value="dropped" <?php if ($enrollment['status'] == 'dropped') echo 'selected'; ?>>Dropped</option>
                    <option value="completed" <?php if ($enrollment['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>
        </div>
        <button type="submit" name="update_enrollment" id="update_enrollment" class="btn_submit">
            <i class="fa-solid fa-floppy-disk"></i> Update Enrollment
        </button>
    </form>
<?php } else { ?>
    <p>Enrollment not found.</p>
<?php } ?>

<?php include('../../layouts/footer.php'); ?>