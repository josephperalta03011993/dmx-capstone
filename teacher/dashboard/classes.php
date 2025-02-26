<?php
$page_title = "My Sections";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Assuming you have a session variable for user_id
$user_id = $_SESSION['user_id'];

// Get the teacher_id from the teachers table using the user_id
$teacher_sql = "SELECT teacher_id FROM teachers WHERE user_id = ?";
$teacher_stmt = mysqli_prepare($conn, $teacher_sql);
mysqli_stmt_bind_param($teacher_stmt, "i", $user_id);
mysqli_stmt_execute($teacher_stmt);
$teacher_result = mysqli_stmt_get_result($teacher_stmt);

if ($teacher_row = mysqli_fetch_assoc($teacher_result)) {
    $teacher_id = $teacher_row['teacher_id'];

    // Fetch sections assigned to the teacher using the teacher_sections table
    $sections_sql = "SELECT sections.section_id, sections.section_name FROM sections INNER JOIN teacher_sections ON sections.section_id = teacher_sections.section_id WHERE teacher_sections.teacher_id = ?";
    $sections_stmt = mysqli_prepare($conn, $sections_sql);
    mysqli_stmt_bind_param($sections_stmt, "i", $teacher_id);
    mysqli_stmt_execute($sections_stmt);
    $sections_result = mysqli_stmt_get_result($sections_stmt);

    ?>

    <h2>My Sections</h2>

    <table id="myTable" class="display">
        <thead>
            <tr>
                <th>Section Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($sections_result) > 0) {
                while ($section = mysqli_fetch_assoc($sections_result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($section['section_name']) . "</td>";
                    echo "<td><a href='teacher_students.php?section_id=" . $section['section_id'] . "'>View Students & Input Grades</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td>No sections found.</td>";
                echo "<td></td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
} else {
    echo "<p>Teacher not found.</p>";
}
?>
<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>
<?php 
    include('../../layouts/footer.php');
?>