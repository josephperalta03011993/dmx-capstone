<?php
    include('../../database/conn.php');
    $page_title = "User Registration - Datamex College of Saint Adeline";

    // Retrieve success and error messages from session
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

    // Clear session messages after retrieval
    unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include('../../layouts/header.php'); ?>   

    <div class="page-header-title">
        <h2>Users</h2>
        <a href='register.php' id='btn_add' class="btn_add"><i class='fa-solid fa-plus'></i> Add New</a>
    </div>    
    <hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

    <?php 
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table id='myTable' class='display'>";
            echo "<thead><tr>";
            echo "<th>Name</th>";
            echo "<th>Username</th>";
            echo "<th>Role</th>";
            echo "<th>Actions</th>";
            echo "</tr></thead><tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['first_name']." ". $row['last_name'] . "</td>";
                echo "<td>".$row['username']."</td>";
                echo "<td>".$row['user_type']."</td>";
                echo "<td>
                        <a href='edit_admin.php?user_id=".$row['user_id']."' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a> 
                        <a href='delete_admin.php?id=".$row['user_id']."' id='btn_del' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i> Delete</a>
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
        return confirm("Are you sure you want to delete this user?");
    }
</script>
<?php include('../../layouts/footer.php'); ?>