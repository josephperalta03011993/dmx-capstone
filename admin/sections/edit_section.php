<?php
include('../../database/conn.php');
$page_title = "Edit Section - Datamex College of Saint Adeline";

$update_success = null;
$update_error = null;

// Check if section_id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $section_id = intval($_GET['id']);

    // Fetch section details for the provided section_id
    $sql = "SELECT * FROM sections WHERE section_id = $section_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $section = $result->fetch_assoc(); // Fetch section data
    } else {
        die("Section not found.");
    }
} else {
    die("Invalid request. Section ID is missing.");
}

// Handle form submission for updating section data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $section_name = sanitize_input($conn, $_POST["section_name"]);
    $section_description = sanitize_input($conn, $_POST["section_description"]);
    $section_status  = sanitize_input($conn, $_POST["section_status"]);

    if (!$update_error) {
        // Update query
        $sql = "UPDATE sections 
                SET section_name = '$section_name', 
                    section_description = '$section_description',
                    section_status = '$section_status'
                    WHERE section_id = $section_id";

        if ($conn->query($sql) === TRUE) {
            $update_success = "Section updated successfully!";
        } else {
            $update_error = "Error updating section: " . $conn->error;
        }
    }
}

?>

<?php include('../../layouts/header.php'); ?>

<h2>Edit Section</h2><hr><br>

<?php if ($update_success) { echo "<p style='color: green;'>$update_success</p>"; } ?>
<?php if ($update_error) { echo "<p style='color: red;'>$update_error</p>"; } ?>

<form method="post" action="">
    <div class="form-container">
        <div class="form-group">
            <label for="section_name">Section Name:</label>
            <input type="text" name="section_name" id="section_name" value="<?php echo $section['section_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="section_description">Description:</label>
            <input type="text" name="section_description" id="section_description" value="<?php echo $section['section_description']; ?>" required>
        </div>
        <div class="form-group">
            <label for="section_status">Status</label>
            <select name="section_status" id="section_status">
                <option value="Active" <?php echo $section['section_status'] == 1 ? 'selected' : ''; ?>>Active</option>
                <option value="Inactive" <?php echo $section['section_status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div class="form-group full-width">
            <button type="submit" name="update" class="btn_submit">
                <i class="fa-solid fa-save"></i> Update Section
            </button>
        </div>
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>
