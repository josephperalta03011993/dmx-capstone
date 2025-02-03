<?php
    include('../../database/conn.php');
    $page_title = "Schedules - Datamex College of Saint Adeline";

    // Retrieve success and error messages from session
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

    // Clear session messages after retrieval
    unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include('../../layouts/header.php'); ?>   

    <div class="page-header-title">
        <h2>Schedules</h2>
        <a href="add_schedule.php" id="btn_add" name="btn_add"><i class="fa-solid fa-plus"></i> Add New</a>
    </div>
    <hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

    <?php 
        $sql = "SELECT s.schedule_id,	s.section_id,	s.day_of_week, s.start_time, s.end_time,
                    sec.section_name
                    FROM schedules AS s 
                    LEFT JOIN sections AS sec ON sec.section_id = s.section_id
                ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table id='myTable' class='display'>";
            echo "<thead><tr>";
            echo "<th>Section</th>";
            echo "<th>Day</th>";
            echo "<th>Start Time</th>";
            echo "<th>End Time</th>";
            echo "<th class='th-action'>Actions</th>";
            echo "</tr></thead><tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['section_name']."</td>";
                echo "<td>".$row['day_of_week']."</td>";
                echo "<td>".$row['start_time']."</td>";
                echo "<td>".$row['end_time']."</td>";
                echo "<td>
                        <a href='edit_schedule.php?id=".$row['schedule_id']."' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a> 
                        <a href='delete_schedule.php?id=".$row['schedule_id']."' id='btn_del' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i> Delete</a>
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
        return confirm("Are you sure you want to delete this schedule?");
    }
</script>
<?php include('../../layouts/footer.php'); ?>