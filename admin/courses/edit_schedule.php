<?php
include('../../database/conn.php');
$page_title = "Edit Schedule - Datamex College of Saint Adeline";

$update_success = null;
$update_error = null;

// Check if schedule_id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $schedule_id = intval($_GET['id']);

    // Fetch schedule details for the provided schedule_id
    $sql = "SELECT * FROM schedules AS s
            LEFT JOIN sections AS sec
            ON sec.section_id = s.section_id
            WHERE schedule_id = $schedule_id
            ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $schedule = $result->fetch_assoc(); // Fetch schedule data
    } else {
        die("Schedule not found.");
    }
} else {
    die("Invalid request. Schedule ID is missing.");
}

// Handle form submission for updating schedule data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $section_id  = sanitize_input($conn, $_POST["reg_section_id"]);
    $day_of_week = sanitize_input($conn, $_POST["reg_day_of_week"]);
    $start_time  = sanitize_input($conn, $_POST["reg_start_time"]);
    $end_time    = sanitize_input($conn, $_POST["reg_end_time"]);

    if (!$update_error) {
        // Update query
        $sql = "UPDATE schedules 
                SET section_id = '$section_id', 
                    day_of_week = '$day_of_week',
                    start_time = '$start_time',
                    end_time = '$end_time' 
                    WHERE schedule_id = $schedule_id";

        if ($conn->query($sql) === TRUE) {
            $update_success = "Schedule updated successfully!";
        } else {
            $update_error = "Error updating schedule: " . $conn->error;
        }
    }
}

?>

<?php include('../../layouts/header.php'); ?>

<h2>Edit Schedule</h2><hr><br>

<?php if ($update_success) { echo "<p style='color: green;'>$update_success</p>"; } ?>
<?php if ($update_error) { echo "<p style='color: red;'>$update_error</p>"; } ?>

<form method="post" action="">
    <div class="form-container">
        <div class="form-group">
            <label for="reg_section_name">Section Name:</label>
            <?php
                $sql_section_list = "SELECT section_id, section_name FROM sections";
                $result = mysqli_query($conn, $sql_section_list);
                if (mysqli_num_rows($result) > 0) {
                    echo "<select id='reg_section_id' name='reg_section_id' required>";
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='".$row['section_id']."'>".$row['section_name']."</option>";
                    }
                    echo "</select><br><br>";
                } else {
                    echo "No section available. <a href='add_section.php'>Create Section</a>";
                }
            ?>
        </div>
        <div class="form-group">
            <label for="reg_day_of_week">Day of Week:</label>
            <select name="reg_day_of_week" id="reg_day_of_week" required>
                <option value="Monday" <?php echo $schedule['day_of_week'] == 'Monday' ? 'selected' : ''?>>Monday</option>
                <option value="Tuesday" <?php echo $schedule['day_of_week'] == 'Tuesday' ? 'selected' : ''?>>Tuesday</option>
                <option value="Wednesday" <?php echo $schedule['day_of_week'] == 'Wednesday' ? 'selected' : ''?>>Wednesday</option>
                <option value="Thursday" <?php echo $schedule['day_of_week'] == 'Thursday' ? 'selected' : ''?>>Thursday</option>
                <option value="Friday" <?php echo $schedule['day_of_week'] == 'Friday' ? 'selected' : ''?>>Friday</option>
                <option value="Saturday" <?php echo $schedule['day_of_week'] == 'Saturday' ? 'selected' : ''?>>Saturday</option>
                <option value="Sunday" <?php echo $schedule['day_of_week'] == 'Sunday' ? 'selected' : ''?>>Sunday</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reg_start_time">Start Time:</label>
            <input type="time" name="reg_start_time" id="reg_start_time" value="<?php echo $schedule['start_time']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_end_time">End Time:</label>
            <input type="time" name="reg_end_time" id="reg_end_time" value="<?php echo $schedule['end_time']; ?>" required>
        </div>
        <div class="form-group full-width">
            <button type="submit" name="update" class="btn_submit">
                <i class="fa-solid fa-save"></i> Update Schedule
            </button>
        </div>
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>