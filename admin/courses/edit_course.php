<?php
include('../../database/conn.php');
$page_title = "Edit Course - Datamex College of Saint Adeline";

$update_success = null;
$update_error = null;

// Check if course_id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $course_id = intval($_GET['id']);

    // Fetch course details for the provided course_id
    $sql = "SELECT * FROM courses WHERE course_id = $course_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc(); // Fetch course data
    } else {
        die("Course not found.");
    }
} else {
    die("Invalid request. Course ID is missing.");
}

// Handle form submission for updating course data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $course_name = sanitize_input($conn, $_POST["reg_course_name"]);
    $course_description = sanitize_input($conn, $_POST["reg_course_description"]);
    $course_code  = sanitize_input($conn, $_POST["reg_course_code"]);
    $credits  = sanitize_input($conn, $_POST["reg_credits"]);
    $semester  = sanitize_input($conn, $_POST["reg_semester"]);
    $is_active  = sanitize_input($conn, $_POST["is_active"]);

    if (!$update_error) {
        // Update query
        $sql = "UPDATE courses 
                SET course_name = '$course_name', 
                    course_description = '$course_description',
                    course_code = '$course_code',
                    credits = '$credits',
                    semester = '$semester',
                    is_active = '$is_active'
                    WHERE course_id = $course_id";

        if ($conn->query($sql) === TRUE) {
            $update_success = "Course updated successfully!";
        } else {
            $update_error = "Error updating course: " . $conn->error;
        }
    }
}

?>

<?php include('../../layouts/header.php'); ?>

<h2>Edit Course</h2><hr><br>

<?php if ($update_success) { echo "<p style='color: green;'>$update_success</p>"; } ?>
<?php if ($update_error) { echo "<p style='color: red;'>$update_error</p>"; } ?>

<form method="post" action="">
    <div class="form-container">
        <div class="form-group">
            <label for="reg_course_name">Course Name:</label>
            <input type="text" name="reg_course_name" id="reg_course_name" value="<?php echo $course['course_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_course_code">Course Code:</label>
            <input type="text" name="reg_course_code" id="reg_course_code" value="<?php echo $course['course_code']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_credits">Credit:</label>
            <input type="number" name="reg_credits" id="reg_credits" value="<?php echo $course['credits']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_semester">Semester</label>
            <select name="reg_semester" id="reg_semester">
                <option value="First Semester" <?php echo $course['semester'] == 'First Semester' ? 'selected' : ''; ?>>First Semester</option>
                <option value="Second Semester" <?php echo $course['semester'] == 'Second Semester' ? 'selected' : ''; ?>>Second Semester</option>
                <option value="Summer" <?php echo $course['semester'] == 'Summer' ? 'selected' : ''; ?>>Summer</option>
            </select>
        </div>
        <div class="form-group">
            <label for="is_active">Active</label>
            <select name="is_active" id="is_active">
                <option value="1" <?php echo $course['is_active'] == 1 ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo $course['is_active'] == 0 ? 'selected' : ''; ?>>Not Active</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reg_course_description">Course Description:</label>
            <textarea name="reg_course_description" id="reg_course_description" rows="4" cols="50">
                <?php echo $course['course_description']; ?>
            </textarea>
        </div>
        <div class="form-group full-width">
            <button type="submit" name="update" class="btn_submit">
                <i class="fa-solid fa-save"></i> Update Course
            </button>
        </div>
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>
