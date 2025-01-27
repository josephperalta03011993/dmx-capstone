<?php
    include('../../database/conn.php');
    $page_title = "User Registration - Datamex College of Saint Adeline";
    $success = null;
    $error = null;

?>

<?php include('../../layouts/header.php'); ?>

    <h2>Users</h2><hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$registration_success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$registration_error</p>"; } ?>

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
                echo "<td><a href='edit_admin.php?id=".$row['user_id']."' id='btn_edit'>Edit</a> <a href='delete_admin.php?id=".$row['user_id']."' id='btn_del'>Delete</a></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "0 results";
        }
    ?> 

<?php include('../../layouts/footer.php'); ?>