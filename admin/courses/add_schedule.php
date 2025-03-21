<?php 
    $page_title = "Admin Dashboard";
    include_once('../../database/conn.php');
    include_once('../../layouts/header.php');

    $success = null;
    $error = null;

    // save schedule request
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_course"])) {
        $section_id = sanitize_input($conn, $_POST["section_id"]);
        $day_of_week = sanitize_input($conn, $_POST["day_of_week"]);
        $start_time = sanitize_input($conn, $_POST["start_time"]);
        $end_time = sanitize_input($conn, $_POST["end_time"]);

        $sql = "INSERT INTO schedules (section_id, start_time, end_time, day_of_week) 
                VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if($stmt) {
            // bind the param
            mysqli_stmt_bind_param($stmt, "ssss", $section_id, $start_time, $end_time, $day_of_week);

            // execute the statement
            if(mysqli_stmt_execute($stmt)) {
                $success = "Schedule created successfully!";
            } else {
                $error = "Error creating schedule: " . mysqli_error($conn);
            }
        }
    }

?>

<h2>Schedule</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-container">
        <div class="form-group">
            <label for="section_id">Section:</label>
            <?php
                $sql_section_list = "SELECT s.section_id, s.section_name, c.course_name 
                                    FROM sections s 
                                    LEFT JOIN courses c ON s.section_id = c.course_id";
                $result = mysqli_query($conn, $sql_section_list);
                if (mysqli_num_rows($result) > 0) {
                    echo "<select id='section_id' name='section_id' required>";
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='".$row['section_id']."'>".$row['section_name'].' - '.$row['course_name']."</option>";
                    }
                    echo "</select><br><br>";
                } else {
                    echo "No section available. <a href='../sections/add_section.php'>Create Section</a>";
                }
            ?>
        </div>
        <div class="form-group">
            <label for="day_of_week">Day:</label>
            <select id="day_of_week" name="day_of_week" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select><br><br>
        </div>
        <div class="form-group">
            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" required><br><br>
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" required><br><br>
        </div>
    </div>
    <button type="submit" name="create_course" id="create_course" class="btn_submit">
        <i class="fa-solid fa-floppy-disk"></i> Create Schedule
    </button>

</form>

<?php include('../../layouts/footer.php'); ?>