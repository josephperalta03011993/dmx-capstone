<?php
$page_title = "Student Grades";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Fetch sections taught by the teacher (using user_id)
$user_id = $_SESSION['user_id'];
$teacher_sql = "SELECT teacher_id FROM teachers WHERE user_id = ?";
$teacher_stmt = mysqli_prepare($conn, $teacher_sql);
mysqli_stmt_bind_param($teacher_stmt, "i", $user_id);
mysqli_stmt_execute($teacher_stmt);
$teacher_result = mysqli_stmt_get_result($teacher_stmt);

if ($teacher_row = mysqli_fetch_assoc($teacher_result)) {
    $teacher_id = $teacher_row['teacher_id'];

    // Fetch sections assigned to the teacher
    $sections_sql = "SELECT sections.section_id, sections.section_description 
                     FROM sections 
                     INNER JOIN teacher_sections ON sections.section_id = teacher_sections.section_id 
                     WHERE teacher_sections.teacher_id = ?";
    $sections_stmt = mysqli_prepare($conn, $sections_sql);
    mysqli_stmt_bind_param($sections_stmt, "i", $teacher_id);
    mysqli_stmt_execute($sections_stmt);
    $sections_result = mysqli_stmt_get_result($sections_stmt);
    $sections = mysqli_fetch_all($sections_result, MYSQLI_ASSOC);

    // Set selected_course_id from GET parameter
    $selected_course_id = isset($_GET['section_id']) ? sanitize_input($conn, $_GET['section_id']) : null;

    // Fetch students enrolled in the selected section
    $students = [];
    if ($selected_course_id) {
        $students_sql = "SELECT students.student_id, students.first_name, students.last_name, students.student_num 
                         FROM students 
                         INNER JOIN enrollments ON students.student_id = enrollments.student_id 
                         WHERE enrollments.section_id = ?";
        $students_stmt = mysqli_prepare($conn, $students_sql);
        mysqli_stmt_bind_param($students_stmt, "i", $selected_course_id);
        mysqli_stmt_execute($students_stmt);
        $students_result = mysqli_stmt_get_result($students_stmt);
        $students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);
    }

    // Fetch existing grades for the selected section
    $grades = [];
    if ($selected_course_id) {
        foreach ($students as $student) {
            $student_id = $student['student_id'];
            $enrollment_sql = "SELECT enrollment_id 
                               FROM enrollments 
                               WHERE student_id = ? AND section_id = ?";
            $enrollment_stmt = mysqli_prepare($conn, $enrollment_sql);
            mysqli_stmt_bind_param($enrollment_stmt, "ii", $student_id, $selected_course_id);
            mysqli_stmt_execute($enrollment_stmt);
            $enrollment_result = mysqli_stmt_get_result($enrollment_stmt);

            if ($enrollment_row = mysqli_fetch_assoc($enrollment_result)) {
                $enrollment_id = $enrollment_row['enrollment_id'];
                $grade_sql = "SELECT prelim, midterm, pre_final, finals, average, remarks 
                              FROM grades 
                              WHERE enrollment_id = ? AND section_id = ?";
                $grade_stmt = mysqli_prepare($conn, $grade_sql);
                mysqli_stmt_bind_param($grade_stmt, "ii", $enrollment_id, $selected_course_id);
                mysqli_stmt_execute($grade_stmt);
                $grade_result = mysqli_stmt_get_result($grade_stmt);

                if ($grade_row = mysqli_fetch_assoc($grade_result)) {
                    $grades[$student_id] = $grade_row;
                }
            }
        }
    }

    // Handle grade submissions
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_grades"])) {
        foreach ($_POST['grades'] as $student_id => $student_grades) {
            $enrollment_sql = "SELECT enrollment_id 
                               FROM enrollments 
                               WHERE student_id = ? AND section_id = ?";
            $enrollment_stmt = mysqli_prepare($conn, $enrollment_sql);
            mysqli_stmt_bind_param($enrollment_stmt, "ii", $student_id, $selected_course_id);
            mysqli_stmt_execute($enrollment_stmt);
            $enrollment_result = mysqli_stmt_get_result($enrollment_stmt);

            if ($enrollment_row = mysqli_fetch_assoc($enrollment_result)) {
                $enrollment_id = $enrollment_row['enrollment_id'];

                // Sanitize and clamp grade values between 0 and 100
                $prelim = min(100, max(0, sanitize_input($conn, $student_grades['prelim'])));
                $midterm = min(100, max(0, sanitize_input($conn, $student_grades['midterm'])));
                $pre_final = min(100, max(0, sanitize_input($conn, $student_grades['pre_final'])));
                $finals = min(100, max(0, sanitize_input($conn, $student_grades['finals'])));
                $remarks = sanitize_input($conn, $student_grades['remarks']);

                // Calculate average
                $average = ($prelim + $midterm + $pre_final + $finals) / 4;

                // Check if grade exists
                $grade_check_sql = "SELECT grade_id 
                                    FROM grades 
                                    WHERE enrollment_id = ? AND section_id = ?";
                $grade_check_stmt = mysqli_prepare($conn, $grade_check_sql);
                mysqli_stmt_bind_param($grade_check_stmt, "ii", $enrollment_id, $selected_course_id);
                mysqli_stmt_execute($grade_check_stmt);
                $grade_check_result = mysqli_stmt_get_result($grade_check_stmt);

                if (mysqli_num_rows($grade_check_result) > 0) {
                    // Update existing grade
                    $update_grade_sql = "UPDATE grades 
                                         SET prelim = ?, midterm = ?, pre_final = ?, finals = ?, average = ?, remarks = ?
                                         WHERE enrollment_id = ? AND section_id = ?";
                    $update_grade_stmt = mysqli_prepare($conn, $update_grade_sql);
                    mysqli_stmt_bind_param($update_grade_stmt, "ddddssii", $prelim, $midterm, $pre_final, $finals, $average, $remarks, $enrollment_id, $selected_course_id);
                    mysqli_stmt_execute($update_grade_stmt);
                } else {
                    // Insert new grade
                    $insert_grade_sql = "INSERT INTO grades (enrollment_id, section_id, prelim, midterm, pre_final, finals, average, remarks) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $insert_grade_stmt = mysqli_prepare($conn, $insert_grade_sql);
                    mysqli_stmt_bind_param($insert_grade_stmt, "iiddddss", $enrollment_id, $selected_course_id, $prelim, $midterm, $pre_final, $finals, $average, $remarks);
                    mysqli_stmt_execute($insert_grade_stmt);
                }
            }
        }
        // Redirect to the same page with the current section_id to reload the updated data
        header("Location: teacher_students.php?section_id=" . urlencode($selected_course_id));
        exit(); // Ensure no further code executes after the redirect
    }
}
?>

<!-- HTML Section -->
<h2>Student Grades</h2>

<!-- Course Selection Form -->
<form method="get">
    <label for="section_id">Select Course:</label>
    <select name="section_id" id="section_id" onchange="this.form.submit()">
        <option value="">Select Course</option>
        <?php foreach ($sections as $course) { ?>
            <option value="<?php echo $course['section_id']; ?>" 
                    <?php if ($selected_course_id == $course['section_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($course['section_description']); ?>
            </option>
        <?php } ?>
    </select>
</form>

<!-- Grades Input Form -->
<?php if ($selected_course_id) { ?>
    <form method="post">
                <?php
                if (!empty($students)) {
                ?>
                <table id="myTable" class="display">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Prelim</th>
                            <th>Midterm</th>
                            <th>Pre-Final</th>
                            <th>Finals</th>
                            <th>Average Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                    foreach ($students as $student) {
                        $student_id = $student['student_id'];
                        $grade_data = isset($grades[$student_id]) ? $grades[$student_id] : [
                            'prelim' => '', 
                            'midterm' => '', 
                            'pre_final' => '', 
                            'finals' => '', 
                            'average' => '',
                            'remarks' => ''
                        ];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($student['first_name'] . " " . $student['last_name']) . (!empty($student['student_num']) ? " (".$student['student_num'].")" : '') . "</td>";
                        echo "<td><input type='number' name='grades[" . $student_id . "][prelim]' step='0.01' min='0' max='100' value='" . htmlspecialchars($grade_data['prelim']) . "'></td>";
                        echo "<td><input type='number' name='grades[" . $student_id . "][midterm]' step='0.01' min='0' max='100' value='" . htmlspecialchars($grade_data['midterm']) . "'></td>";
                        echo "<td><input type='number' name='grades[" . $student_id . "][pre_final]' step='0.01' min='0' max='100' value='" . htmlspecialchars($grade_data['pre_final']) . "'></td>";
                        echo "<td><input type='number' name='grades[" . $student_id . "][finals]' step='0.01' min='0' max='100' value='" . htmlspecialchars($grade_data['finals']) . "'></td>";
                        echo "<td><input type='number' name='grades[" . $student_id . "][finals]' step='0.01' min='0' max='100' value='" . htmlspecialchars($grade_data['average']) . "' disabled></td>";
                        echo "<td><input type='text' name='grades[" . $student_id . "][remarks]' value='" . htmlspecialchars($grade_data['remarks']) . "'></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br>
        <button type="submit" name="save_grades" id="btn_add" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Grades</button>
    </form>
<?php } ?>

<!-- JavaScript for Page Title -->
<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>

<?php include('../../layouts/footer.php'); ?>