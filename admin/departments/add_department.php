<?php
include('../../database/conn.php');
$page_title = "Department - Datamex College of Saint Adeline";
$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_new_department"])) {
    $department_name = sanitize_input($conn, $_POST["reg_department_name"]);
    $department_description = sanitize_input($conn, $_POST["reg_department_description"]);

    // Basic Validation (Improve this significantly in a real app)
    if (empty($department_name) || empty($department_description)) {
        $error = "All fields are required.";
    }
        //Check if department name already exist
        $check_department_sql = "SELECT department_name 
                                FROM departments 
                                WHERE department_name  = '$department_name'";
        $check_result = $conn->query($check_department_sql);

        if ($check_result->num_rows > 0){
            $error = "Department name already exist.";
        } else {
            $sql = "INSERT INTO departments (department_name, department_description) 
                    VALUES ('$department_name', '$department_description')";

            if ($conn->query($sql) === TRUE) {
                $success = "Created new department successful!";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }

    }

?>

<?php include('../../layouts/header.php'); ?>

    <h2>Create New Department</h2><hr><br>

    <?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
    <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="department-form-container">
            <div class="one-col-form-group">
                <label for="reg_department_name">Department Name:</label>
                <input type="text" name="reg_department_name" id="reg_department_name" required>
            </div>
            <div class="one-col-form-group">
                <label for="reg_department_description">Department Description:</label>
                <input type="text" name="reg_department_description" id="reg_department_description" required>
            </div>
            <div class="one-col-form-group full-width">
                <button type="submit" name="add_new_department" class="btn_submit">
                    <i class="fa-solid fa-floppy-disk"></i> Create Department
                </button>
            </div>
        </div>
    </form>

<?php include('../../layouts/footer.php'); ?>