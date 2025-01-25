<?php 
    $page_title = "Admin Dashboard";
    include_once('../../database/conn.php');
    include_once('../../layouts/header.php');

    $success = null;
    $error = null;

    // save announcement request
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_announcement"])) {
        $title = sanitize_input($conn, $_POST["announcement_title"]);
        $content = sanitize_input($conn, $_POST["announcement_content"]);
        $startDate = sanitize_input($conn, $_POST["announcement_start_date"]);
        $endDate = sanitize_input($conn, $_POST["announcement_end_date"]);
        $createdAt = date("Y-m-d H:i:s");
        $updatedAt = $createdAt; // initially the same value
        $createdBy = $_SESSION["user_id"];

        $sql = "INSERT INTO announcements (title, created_at, updated_at, content, start_date, end_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if($stmt) {
            // bind the param
            mysqli_stmt_bind_param($stmt, "sssssss", $title, $createdAt, $updatedAt, $content, $startDate, $endDate, $createdBy);

            // execute the statement
            if(mysqli_stmt_execute($stmt)) {
                $success = "Announcement created successfully!";
            } else {
                $error = "Error creating announcement: " . mysqli_error($conn);
            }
        }
    }

?>

<h2>Annoucement</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="announcement_title">Title:</label>
    <input type="text" id="announcement_title" name="announcement_title" required><br>

    <label for="announcement_content">Content:</label>
    <textarea id="announcement_content" name="announcement_content" rows="4" cols="50" required></textarea><br><br>

    <label for="announcement_start_date">Start Date:</label>
    <input type="date" id="announcement_start_date" name="announcement_start_date" required><br><br>

    <label for="announcement_end_date">End Date:</label>
    <input type="date" id="announcement_end_date" name="announcement_end_date" required><br><br>

    <input type="submit" name="create_announcement" id="create_announcement" value="Create Announcement">
</form>

<?php include('../../layouts/footer.php'); ?>