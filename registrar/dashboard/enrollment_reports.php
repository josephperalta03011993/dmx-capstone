<?php
$page_title = "Enrollment Report";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Get enrollments per section (with section_id for details)
$enrollments_sql = "SELECT sec.section_id, sec.section_name, COUNT(e.enrollment_id) AS enrollment_count, r.room_name, r.capacity
                    FROM enrollments e
                    JOIN sections sec ON e.section_id = sec.section_id
                    JOIN rooms r ON e.room_id = r.room_id
                    GROUP BY sec.section_id";

$enrollments_result = $conn->query($enrollments_sql);
?>

<h2>Enrollment Report</h2>

<!-- Custom Modal for displaying student list (hidden by default) -->
<div id="studentModal" class="custom-modal" style="display: none;">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">Students Enrolled in Section</h5>
            <button class="custom-modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="custom-modal-body" id="studentList">
            <!-- Student list will be populated here via AJAX or PHP -->
            <p>Loading...</p>
        </div>
        <div class="custom-modal-footer">
            <button class="custom-modal-close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<table id='myTable' class='display'>
    <thead>
        <tr>
            <th>Section Name</th>
            <th>Number of Enrollees</th>
            <th>Room Name</th>
            <th>Room Capacity</th>
            <th>Actions</th> <!-- New column for View Details -->
        </tr>
    </thead>
    <tbody>
        <?php
        if ($enrollments_result->num_rows > 0) {
            while ($row = $enrollments_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['section_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['enrollment_count']) . "</td>";
                echo "<td>" . htmlspecialchars($row['room_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['capacity']) . "</td>";
                echo "<td><button class='view-details' data-section-id='" . htmlspecialchars($row['section_id']) . "'><i class='fa-solid fa-eye'></i> View Details</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No enrollment data found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Pass page title to JS -->
<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>
<?php include('../../layouts/footer.php'); ?>