<?php
    include('../../database/conn.php');
    $page_title = "Contact Messages - Datamex College of Saint Adeline";

    // Retrieve success and error messages from session
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

    // Clear session messages after retrieval
    unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include('../../layouts/header.php'); ?>

    <div class="page-header-title">
        <h2>Contact Messages</h2>
        </div>
    <hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

    <?php
        $sql = "SELECT * FROM contact_us ORDER BY created_at DESC"; // Order by newest first
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table id='myTable' class='display'>";
            echo "<thead><tr>";
            echo "<th>Name</th>";
            echo "<th>Email</th>";
            echo "<th>Message</th>";
            echo "<th>Date Received</th>";
            echo "</tr></thead><tbody>";
            while($row = $result->fetch_assoc()) {
                // Format the created_at timestamp to 12-hour format
                $timestamp = strtotime($row['created_at']);
                $formatted_date = date("F j, Y h:i A", $timestamp);

                echo "<tr>";
                echo "<td>".$row['name']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".nl2br($row['message'])."</td>"; // Use nl2br to preserve line breaks
                echo "<td>".$formatted_date."</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "No contact messages received yet.";
        }
    ?>
<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this contact message?");
    }
</script>
<?php include('../../layouts/footer.php'); ?>