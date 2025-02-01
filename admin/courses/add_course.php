<?php 
    $page_title = "Admin Dashboard";
    include_once('../../database/conn.php');
    include_once('../../layouts/header.php');

    $success = null;
    $error = null;

    // save course request
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_course"])) {
        $course_code = sanitize_input($conn, $_POST["course_code"]);
        $course_name = sanitize_input($conn, $_POST["course_name"]);
        $credits = sanitize_input($conn, $_POST["credits"]);
        $semester = sanitize_input($conn, $_POST["semester"]);
        $course_level = sanitize_input($conn, $_POST["course_level"]);
        $course_description = sanitize_input($conn, $_POST["course_description"]);
        //$createdAt = date("Y-m-d H:i:s");
        //$updatedAt = $createdAt; // initially the same value
        //$createdBy = $_SESSION["user_id"];

        $sql = "INSERT INTO courses (course_code, course_name, course_description, credits, 
                course_level, semester) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if($stmt) {
            // bind the param
            mysqli_stmt_bind_param($stmt, "ssssss", $course_code, $course_name, 
                            $course_description, $credits, $course_level, $semester);

            // execute the statement
            if(mysqli_stmt_execute($stmt)) {
                $success = "Course created successfully!";
            } else {
                $error = "Error creating course: " . mysqli_error($conn);
            }
        }
    }

?>

<h2>Course</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-container">
        <div class="form-group">
            <label for="course_code">Course Code:</label>
            <input type="text" id="course_code" name="course_code" required><br>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" required><br>
        </div>
        <div class="form-group">
            <label for="credits">Credits:</label>
            <input type="number" id="credits" name="credits" min="1" max="4" required><br><br>
        </div>
        <div class="form-group">
            <label for="course_level">Course Level:</label>
            <select id="course_level" name="course_level" required>
                <option value="Undergraduate">Undergraduate</option>
                <option value="Graduate">Graduate</option>
                <option value="Postgraduate">Postgraduate</option>
            </select><br><br>
        </div>
        <div class="form-group">
            <label for="course_description">Course Description:</label>
            <textarea id="course_description" name="course_description" rows="4" cols="50" required></textarea><br><br>
        </div>
        <div class="form-group">
            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="First Semester">1st Semester</option>
                <option value="Second Semester">2nd Semester</option>
                <option value="Summer">Summer</option>
            </select><br><br>
        </div>
    </div>
    <button type="submit" name="create_course" id="create_course" class="btn_submit">
        <i class="fa-solid fa-floppy-disk"></i> Create Course
    </button>

</form>

<?php include('../../layouts/footer.php'); ?>