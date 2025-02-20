<?php 
    $page_title = "Add Section";
    include_once('../../database/conn.php');
    include_once('../../layouts/header.php');

    $success = null;
    $error = null;

    // save course request
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_section"])) {
        $section_name = sanitize_input($conn, $_POST["section_name"]);
        $section_description = sanitize_input($conn, $_POST["section_description"]);
        $section_status = sanitize_input($conn, $_POST['section_status']);

        $sql = "INSERT INTO sections (section_name, section_description, section_status) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if($stmt) {
            // bind the param
            mysqli_stmt_bind_param($stmt, "sss", $section_name, $section_description, $section_status);

            // execute the statement
            if(mysqli_stmt_execute($stmt)) {
                $success = "Section created successfully!";
            } else {
                $error = "Error creating section: " . mysqli_error($conn);
            }
        }
    }

?>

<h2>Section</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="section-form-container">
        <div class="one-col-form-group">
            <label for="section_name">Section Name:</label>
            <input type="text" id="section_name" name="section_name" required><br>
        </div>
        <div class="one-col-form-group">
            <label for="section_description">Description:</label>
            <input type="text" id="section_description" name="section_description" required><br>
        </div>
        <div class="one-col-form-group">
            <label for="section_status">Status</label>
            <select name="section_status" id="section_status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>
    <button type="submit" name="create_section" id="create_section" class="btn_submit">
        <i class="fa-solid fa-floppy-disk"></i> Create Section
    </button>

</form>

<?php include('../../layouts/footer.php'); ?>