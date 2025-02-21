<?php
include('../../database/conn.php');
$page_title = "Manage Enrollment - Datamex College of Saint Adeline";

// Retrieve success and error messages from session
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Clear session messages after retrieval
unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include('../../layouts/header.php'); ?>

<div class="page-header-title">
    <h2>Manage Enrollment</h2>
    <a href="add_enrollment.php" id="btn_add" name="btn_add"><i class="fa-solid fa-plus"></i> Add New Enrollment</a>
</div>
<hr><br>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<?php
$sql = "SELECT e.enrollment_id, s.first_name, s.last_name, c.course_name, r.room_name, sec.section_name, e.enrollment_date, e.status, sch.day_of_week, sch.start_time, sch.end_time
        FROM enrollments e
        JOIN students s ON e.student_id = s.student_id
        JOIN sections sec ON e.section_id = sec.section_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN rooms r ON e.room_id = r.room_id
        JOIN schedules sch ON sec.section_id = sch.section_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table id='myTable' class='display'>";
    echo "<thead><tr>";
    echo "<th>Student Name</th>";
    echo "<th>Course</th>";
    echo "<th>Room</th>";
    echo "<th>Section</th>";
    echo "<th>Day</th>";
    echo "<th>Start Time</th>";
    echo "<th>End Time</th>";
    echo "<th>Enrollment Date</th>";
    echo "<th>Status</th>";
    echo "<th class='th-action'>Actions</th>";
    echo "</tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
        echo "<td>" . $row['course_name'] . "</td>";
        echo "<td>" . $row['room_name'] . "</td>";
        echo "<td>" . $row['section_name'] . "</td>";
        echo "<td>" . $row['day_of_week'] . "</td>";
        echo "<td>" . date("h:i A", strtotime($row['start_time'])) . "</td>"; // 12-hour format
        echo "<td>" . date("h:i A", strtotime($row['end_time'])) . "</td>"; // 12-hour format
        echo "<td>" . $row['enrollment_date'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>
                <a href='edit_enrollment.php?id=" . $row['enrollment_id'] . "' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a> 
                <a href='delete_enrollment.php?id=" . $row['enrollment_id'] . "' id='btn_del' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i> Delete</a>
            </td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "0 results";
}
?>
<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this enrollment?");
    }
</script>
<?php include('../../layouts/footer.php'); ?>