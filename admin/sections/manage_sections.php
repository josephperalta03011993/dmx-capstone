<?php
    include('../../database/conn.php');
    $page_title = "Sections - Datamex College of Saint Adeline";

    // Retrieve success and error messages from session
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

    // Clear session messages after retrieval
    unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include('../../layouts/header.php'); ?>   

    <div class="page-header-title">
        <h2>Sections</h2>
        <a href="add_section.php" id="btn_add" name="btn_add"><i class="fa-solid fa-plus"></i> Add New</a>
    </div>
    <hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

    <?php 
        $sql = "SELECT s.section_id, s.course_id, s.instructor_id, s.room_id, s.section_name,
                    c.course_name AS course, 
                    t.first_name AS instructor_first_name,
                    t.last_name AS instructor_last_name,
                    r.room_name AS room,
                    s.section_name AS section 
                    FROM sections AS s 
                    LEFT JOIN courses AS c ON c.course_id = s.course_id
                    LEFT JOIN teachers AS t ON s.instructor_id = t.teacher_id
                    LEFT JOIN rooms AS r ON s.room_id = r.room_id
                ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table id='myTable' class='display'>";
            echo "<thead><tr>";
            echo "<th>Course</th>";
            echo "<th>Instructor</th>";
            echo "<th>Room</th>";
            echo "<th>Section</th>";
            echo "<th class='th-action'>Actions</th>";
            echo "</tr></thead><tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['course']."</td>";
                echo "<td>".$row['instructor_first_name'].' '.$row['instructor_last_name']."</td>";
                echo "<td>".$row['room']."</td>";
                echo "<td>".$row['section']."</td>";
                echo "<td>
                        <a href='edit_section.php?id=".$row['section_id']."' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a> 
                        <a href='delete_section.php?id=".$row['section_id']."' id='btn_del' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i> Delete</a>
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
        return confirm("Are you sure you want to delete this section?");
    }
</script>
<?php include('../../layouts/footer.php'); ?>