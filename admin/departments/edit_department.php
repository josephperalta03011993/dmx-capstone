<?php
include('../../database/conn.php');
$page_title = "Edit Department - Datamex College of Saint Adeline";

$update_success = null;
$update_error = null;

// Check if department_id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $department_id = intval($_GET['id']);

    // Fetch department details for the provided department_id
    $sql = "SELECT * FROM departments WHERE department_id = $department_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $department = $result->fetch_assoc(); // Fetch department data
    } else {
        die("Department not found.");
    }
} else {
    die("Invalid request. Department ID is missing.");
}

// Handle form submission for updating department data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $department_name = sanitize_input($conn, $_POST["reg_department_name"]);
    $department_description = sanitize_input($conn, $_POST["reg_department_description"]);

    if (!$update_error) {
        // Update query
        $sql = "UPDATE departments 
                SET department_name = '$department_name', 
                    department_description = '$department_description'
                    WHERE department_id = $department_id";

        if ($conn->query($sql) === TRUE) {
            $update_success = "Department updated successfully!";
        } else {
            $update_error = "Error updating department: " . $conn->error;
        }
    }
}

?>

<?php include('../../layouts/header.php'); ?>

<h2>Edit Department</h2><hr><br>

<?php if ($update_success) { echo "<p style='color: green;'>$update_success</p>"; } ?>
<?php if ($update_error) { echo "<p style='color: red;'>$update_error</p>"; } ?>

<form method="post" action="">
    <div class="deparment-form-container">
        <div class="one-col-form-group">
            <label for="reg_department_name">Department Name:</label>
            <input type="text" name="reg_department_name" id="reg_department_name" value="<?php echo $department['department_name']; ?>" required>
        </div>
        <div class="one-col-form-group">
            <label for="reg_department_description">Department Description:</label>
            <textarea name="reg_department_description" id="reg_department_description" rows="4" cols="50"><?php echo $department['department_description']; ?></textarea>
        </div>
        <div class="one-col-form-group full-width">
            <button type="submit" name="update" class="btn_submit">
                <i class="fa-solid fa-save"></i> Update Department
            </button>
        </div>
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>
