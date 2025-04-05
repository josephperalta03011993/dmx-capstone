<?php
$page_title = "Student Grades - Admin View";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Fetch all courses with filter option
$filter = isset($_GET['filter']) ? $_GET['filter'] : '1'; // Default to Active (1)
$course_sql = "SELECT course_id, course_code, course_name 
               FROM courses 
               WHERE is_active = ? OR ? = 0"; // 0 for All, 1 for Active
$course_stmt = mysqli_prepare($conn, $course_sql);
mysqli_stmt_bind_param($course_stmt, "ii", $filter, $filter);
mysqli_stmt_execute($course_stmt);
$course_result = mysqli_stmt_get_result($course_stmt);
$courses = mysqli_fetch_all($course_result, MYSQLI_ASSOC);

// Set selected course from GET parameter
$selected_course_id = isset($_GET['course_id']) ? sanitize_input($conn, $_GET['course_id']) : null;

// Fetch students and their grades for the selected course
$students = [];
$grades = [];
if ($selected_course_id) {
    // Get students enrolled in the course through sections and enrollments
    $students_sql = "SELECT DISTINCT s.student_id, s.first_name, s.last_name, s.student_num
                    FROM students s
                    INNER JOIN enrollments e ON s.student_id = e.student_id
                    INNER JOIN sections sec ON e.section_id = sec.section_id
                    WHERE sec.course_id = ?";
    $students_stmt = mysqli_prepare($conn, $students_sql);
    mysqli_stmt_bind_param($students_stmt, "i", $selected_course_id);
    mysqli_stmt_execute($students_stmt);
    $students_result = mysqli_stmt_get_result($students_stmt);
    $students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);

    // Fetch grades for all students in the selected course
    foreach ($students as $student) {
        $student_id = $student['student_id'];
        $grade_sql = "SELECT g.prelim, g.midterm, g.pre_final, g.finals, g.average, g.remarks
                     FROM grades g
                     INNER JOIN enrollments e ON g.enrollment_id = e.enrollment_id
                     INNER JOIN sections sec ON e.section_id = sec.section_id
                     WHERE e.student_id = ? AND sec.course_id = ?";
        $grade_stmt = mysqli_prepare($conn, $grade_sql);
        mysqli_stmt_bind_param($grade_stmt, "ii", $student_id, $selected_course_id);
        mysqli_stmt_execute($grade_stmt);
        $grade_result = mysqli_stmt_get_result($grade_stmt);

        if ($grade_row = mysqli_fetch_assoc($grade_result)) {
            $grades[$student_id] = $grade_row;
        } else {
            $grades[$student_id] = [
                'prelim' => '', 
                'midterm' => '', 
                'pre_final' => '', 
                'finals' => '', 
                'average' => '',
                'remarks' => ''
            ];
        }
    }
}
?>

<!-- HTML Section -->
<h2>Student Grades - Admin View</h2>

<!-- Filter and Course Selection Form -->
<form method="get">
    <div style="margin-bottom: 15px;">
        <label for="filter">Filter Courses:</label>
        <select name="filter" id="filter" onchange="this.form.submit()">
            <option value="1" <?php if ($filter == '1') echo 'selected'; ?>>Active Only</option>
            <option value="0" <?php if ($filter == '0') echo 'selected'; ?>>All Courses</option>
        </select>
    </div>

    <label for="course_id">Select Course:</label>
    <select name="course_id" id="course_id" onchange="this.form.submit()">
        <option value="">Select Course</option>
        <?php foreach ($courses as $course) { ?>
            <option value="<?php echo $course['course_id']; ?>" 
                    <?php if ($selected_course_id == $course['course_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($course['course_code'] . " - " . $course['course_name']); ?>
            </option>
        <?php } ?>
    </select>
</form>

<!-- Grades Display Table -->
<?php if ($selected_course_id) { ?>
    <div style="margin-top: 20px;">
        <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student Number</th>
                    <th>Prelim</th>
                    <th>Midterm</th>
                    <th>Pre-Final</th>
                    <th>Finals</th>
                    <th>Average</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($students)) {
                    foreach ($students as $student) {
                        $student_id = $student['student_id'];
                        $grade_data = $grades[$student_id];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($student['first_name'] . " " . $student['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($student['student_num']) . "</td>";
                        echo "<td>" . htmlspecialchars($grade_data['prelim']) . "</td>";
                        echo "<td>" . htmlspecialchars($grade_data['midterm']) . "</td>";
                        echo "<td>" . htmlspecialchars($grade_data['pre_final']) . "</td>";
                        echo "<td>" . htmlspecialchars($grade_data['finals']) . "</td>";
                        echo "<td>" . htmlspecialchars($grade_data['average']) . "</td>";
                        echo "<td>" . htmlspecialchars($grade_data['remarks']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr>";
                    echo "<td>No students found for this course</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<!-- JavaScript for Page Title -->
<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>

<?php include('../../layouts/footer.php'); ?>