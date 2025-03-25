<?php
include('../../database/conn.php'); // Adjust path
$page_title = "Assign Courses to Sections - Datamex College of Saint Adeline";

// Check if user is admin or registrar
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['admin', 'registrar'])) {
    header("Location: ../../login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_id = $_POST['section_id'];
    $course_id = $_POST['course_id'];
    $room_id = $_POST['room_id'] ?: NULL; // Allow NULL if no room selected

    $update_sql = "UPDATE sections SET course_id = ?, room_id = ? WHERE section_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "iii", $course_id, $room_id, $section_id);
    if (mysqli_stmt_execute($update_stmt)) {
        $success = "Course and room assigned to section successfully!";
    } else {
        $error = "Failed to assign: " . mysqli_error($conn);
    }
}

// Fetch all sections
$sections_sql = "SELECT section_id, section_name, course_id, room_id FROM sections";
$sections_result = mysqli_query($conn, $sections_sql);
$sections = mysqli_fetch_all($sections_result, MYSQLI_ASSOC);

// Fetch all courses
$courses_sql = "SELECT course_id, course_code, course_name FROM courses";
$courses_result = mysqli_query($conn, $courses_sql);
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

// Fetch all rooms
$rooms_sql = "SELECT room_id, room_name, capacity FROM rooms";
$rooms_result = mysqli_query($conn, $rooms_sql);
$rooms = mysqli_fetch_all($rooms_result, MYSQLI_ASSOC);

?>

<?php include('../../layouts/header.php'); ?>

    <h2>Assign Courses to Sections</h2><hr><br>

    <?php if (isset($success)) { ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php } elseif (isset($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST">
        <label for="section_id">Select Section:</label>
        <select name="section_id" id="section_id" required>
            <option value="">-- Select a Section --</option>
            <?php foreach ($sections as $section) { ?>
                <option value="<?php echo $section['section_id']; ?>">
                    <?php 
                    $assignment_info = $section['section_name'];
                    if ($section['course_id']) {
                        $assignment_info .= " (Course ID: {$section['course_id']}";
                        $assignment_info .= $section['room_id'] ? ", Room ID: {$section['room_id']})" : ")";
                    } else {
                        $assignment_info .= " (No Course Assigned)";
                    }
                    echo htmlspecialchars($assignment_info);
                    ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label for="course_id">Select Course:</label>
        <select name="course_id" id="course_id" required>
            <option value="">-- Select a Course --</option>
            <?php foreach ($courses as $course) { ?>
                <option value="<?php echo $course['course_id']; ?>">
                    <?php echo htmlspecialchars($course['course_code'] . " - " . $course['course_name']); ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label for="room_id">Select Room (Optional):</label>
        <select name="room_id" id="room_id">
            <option value="">-- No Room Assigned --</option>
            <?php foreach ($rooms as $room) { ?>
                <option value="<?php echo $room['room_id']; ?>">
                    <?php echo htmlspecialchars($room['room_name'] . " (Capacity: " . $room['capacity'] . ")"); ?>
                </option>
            <?php } ?>
        </select><br><br>

        <button type="submit" class="btn_submit">Assign Course and Room</button>
    </form>

    <h3>Current Assignments</h3>
    <table id="myTable" class="display">
        <thead>
            <tr>
                <th>Section</th>
                <th>Course</th>
                <th>Room</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sections as $section) { 
                if ($section['course_id']) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($section['section_name']); ?></td>
                        <td><?php 
                            $course = array_filter($courses, function($c) use ($section) { 
                                return $c['course_id'] == $section['course_id']; 
                            });
                            $course = reset($course);
                            echo htmlspecialchars($course['course_code'] . " - " . $course['course_name']); 
                        ?></td>
                        <td><?php 
                            $room = array_filter($rooms, function($r) use ($section) { 
                                return $r['room_id'] == $section['room_id']; 
                            });
                            $room = reset($room);
                            echo htmlspecialchars($room['room_name'] ?? 'TBA'); 
                        ?></td>
                    </tr>
                <?php } 
            } ?>
        </tbody>
    </table>

<?php include('../../layouts/footer.php'); ?>