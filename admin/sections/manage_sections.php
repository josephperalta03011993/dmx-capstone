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
$sql = "SELECT section_id, section_name, section_description, section_status FROM sections";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table id='myTable' class='display'>";
    echo "<thead><tr>";
    echo "<th>Section Name</th>";
    echo "<th>Description</th>";
    echo "<th>Status</th>";
    echo "<th class='th-action'>Actions</th>";
    echo "</tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['section_name'] . "</td>";
        echo "<td>" . $row['section_description'] . "</td>";
        echo "<td>" . $row['section_status'] . "</td>";
        echo "<td>
                <a href='edit_section.php?id=" . $row['section_id'] . "' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a> 
                <a href='delete_section.php?id=" . $row['section_id'] . "' id='btn_del' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i> Delete</a>
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