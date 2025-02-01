<?php
    include('../../database/conn.php');
    $page_title = "Announcement Page - Datamex College of Saint Adeline";

    // Retrieve success and error messages from session
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

    // Clear session messages after retrieval
    unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include('../../layouts/header.php'); ?>   

    <div class="page-header-title">
        <h2>Announcements</h2>
        <a href='add_announcement.php' id='btn_add' class="btn_add"><i class='fa-solid fa-plus'></i> Add New</a>
    </div>    
    <hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

    <?php 
        $sql = "SELECT * FROM announcements";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table id='myTable' class='display'>";
            echo "<thead><tr>";
            echo "<th>Title</th>";
            echo "<th>Content</th>";
            echo "<th>Start Date</th>";
            echo "<th>End Date</th>";
            echo "<th class='th-action'>Actions</th>";
            echo "</tr></thead><tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['title']."</td>";
                echo "<td>".$row['content']."</td>";
                echo "<td>".$row['start_date']."</td>";
                echo "<td>".$row['end_date']."</td>";
                echo "<td>
                        <a href='edit_announcement.php?id=".$row['announcement_id']."' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a> 
                        <a href='delete_announcement.php?id=".$row['announcement_id']."' id='btn_del' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i> Delete</a>
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
        return confirm("Are you sure you want to delete this announcement?");
    }
</script>
<?php include('../../layouts/footer.php'); ?>