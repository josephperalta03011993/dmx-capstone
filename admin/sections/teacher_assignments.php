<?php
include('../../database/conn.php');
$page_title = "Assign Teachers to Sections - Datamex College of Saint Adeline";

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all teachers
$teachers_sql = "SELECT teacher_id, first_name, last_name FROM teachers";
$teachers_result = mysqli_query($conn, $teachers_sql);
$teachers = mysqli_fetch_all($teachers_result, MYSQLI_ASSOC);

// Fetch all sections with course info
$sections_sql = "SELECT s.section_id, s.section_name, c.course_code, c.course_name 
                 FROM sections s
                 INNER JOIN enrollments e ON s.section_id = e.section_id
                 INNER JOIN courses c ON e.course_id = c.course_id
                 GROUP BY s.section_id";
$sections_result = mysqli_query($conn, $sections_sql);
$sections = mysqli_fetch_all($sections_result, MYSQLI_ASSOC);

// Handle assignment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign'])) {
    $teacher_id = sanitize_input($conn, $_POST['teacher_id']);
    $section_id = sanitize_input($conn, $_POST['section_id']);

    // Check if assignment already exists
    $check_sql = "SELECT teacher_section_id FROM teacher_sections WHERE teacher_id = ? AND section_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $teacher_id, $section_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "This teacher is already assigned to this section.";
    } else {
        $assign_sql = "INSERT INTO teacher_sections (teacher_id, section_id) VALUES (?, ?)";
        $assign_stmt = mysqli_prepare($conn, $assign_sql);
        mysqli_stmt_bind_param($assign_stmt, "ii", $teacher_id, $section_id);
        if (mysqli_stmt_execute($assign_stmt)) {
            $success = "Teacher assigned successfully!";
        } else {
            $error = "Error assigning teacher: " . mysqli_error($conn);
        }
    }
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $teacher_section_id = sanitize_input($conn, $_POST['teacher_section_id']);
    $delete_sql = "DELETE FROM teacher_sections WHERE teacher_section_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $teacher_section_id);
    if (mysqli_stmt_execute($delete_stmt)) {
        $success = "Assignment removed successfully!";
    } else {
        $error = "Error removing assignment: " . mysqli_error($conn);
    }
}

// Fetch current assignments
$assignments_sql = "SELECT ts.teacher_section_id, t.first_name, t.last_name, s.section_name, c.course_code, c.course_name
                    FROM teacher_sections ts
                    INNER JOIN teachers t ON ts.teacher_id = t.teacher_id
                    INNER JOIN sections s ON ts.section_id = s.section_id
                    INNER JOIN enrollments e ON s.section_id = e.section_id
                    INNER JOIN courses c ON e.course_id = c.course_id
                    GROUP BY ts.teacher_section_id";
$assignments_result = mysqli_query($conn, $assignments_sql);
$assignments = mysqli_fetch_all($assignments_result, MYSQLI_ASSOC);

?>

<?php include('../../layouts/header.php'); ?>

    <h2>Assign Teachers to Sections</h2><hr><br>

    <?php if (isset($success)) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <!-- Assignment Form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-container">
            <div class="form-group">
                <label for="teacher_id">Select Teacher:</label>
                <select name="teacher_id" id="teacher_id" required>
                    <option value="">-- Select Teacher --</option>
                    <?php foreach ($teachers as $teacher) { ?>
                        <option value="<?php echo $teacher['teacher_id']; ?>">
                            <?php echo htmlspecialchars($teacher['first_name'] . " " . $teacher['last_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="section_id">Select Section:</label>
                <select name="section_id" id="section_id" required>
                    <option value="">-- Select Section --</option>
                    <?php foreach ($sections as $section) { ?>
                        <option value="<?php echo $section['section_id']; ?>">
                            <?php echo htmlspecialchars($section['course_code'] . " - " . $section['course_name'] . " (" . $section['section_name'] . ")"); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group full-width">
                <button type="submit" name="assign" class="btn_submit">
                    <i class="fa-solid fa-plus"></i> Assign Teacher
                </button>
            </div>
        </div>
    </form>

    <!-- Current Assignments -->
    <h3>Current Assignments</h3><hr><br>
    <?php if (!empty($assignments)) { ?>
        <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['first_name'] . " " . $assignment['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['course_code'] . " - " . $assignment['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['section_name']); ?></td>
                        <td>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return confirm('Are you sure you want to remove this assignment?');">
                                <input type="hidden" name="teacher_section_id" value="<?php echo $assignment['teacher_section_id']; ?>">
                                <button type="submit" name="delete" class="btn" style="background-color: #DC354B; color: white;"><i class='fa-solid fa-trash'></i> Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No teacher-section assignments found.</p>
    <?php } ?>

<?php include('../../layouts/footer.php'); ?>