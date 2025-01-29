<?php
    include('../../database/conn.php');
    $page_title = "Courses - Datamex College of Saint Adeline";

    // Retrieve success and error messages from session
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

    // Clear session messages after retrieval
    unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include('../../layouts/header.php'); ?>   

    <h2>Courses</h2><hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

    <?php 
        $sql = "SELECT * FROM courses";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table id='myTable' class='display'>";
            echo "<thead><tr>";
            echo "<th>Code</th>";
            echo "<th>Course Name</th>";
            echo "<th>Description</th>";
            echo "<th>Credits</th>";
            echo "<th>Semester</th>";
            echo "<th class='th-action'>Actions</th>";
            echo "</tr></thead><tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['course_code']."</td>";
                echo "<td>".$row['course_name']."</td>";
                echo "<td>".$row['course_description']."</td>";
                echo "<td>".$row['credits']."</td>";
                echo "<td>".$row['semester']."</td>";
                echo "<td>
                        <a href='edit_course.php?id=".$row['course_id']."' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a> 
                        <a href='delete_course.php?id=".$row['course_id']."' id='btn_del' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i> Delete</a>
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
        return confirm("Are you sure you want to delete this course?");
    }
</script>
<?php include('../../layouts/footer.php'); ?>