
<?php
$page_title = "Enrollment Report";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Get enrollments per section
$enrollments_sql = "SELECT sec.section_name, COUNT(e.enrollment_id) AS enrollment_count, r.room_name, r.capacity
                    FROM enrollments e
                    JOIN sections sec ON e.section_id = sec.section_id
                    JOIN rooms r ON e.room_id = r.room_id
                    GROUP BY sec.section_id";

$enrollments_result = $conn->query($enrollments_sql);
?>

<h2>Enrollment Report</h2>

<table id='myTable' class='display'>
    <thead>
        <tr>
            <th>Section Name</th>
            <th>Number of Enrollments</th>
            <th>Room Name</th>
            <th>Room Capacity</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($enrollments_result->num_rows > 0) {
            while ($row = $enrollments_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['section_name'] . "</td>";
                echo "<td>" . $row['enrollment_count'] . "</td>";
                echo "<td>" . $row['room_name'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No enrollment data found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include('../../layouts/footer.php'); ?>