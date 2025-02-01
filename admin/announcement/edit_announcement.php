<?php
include('../../database/conn.php');
$page_title = "Edit Announcement - Datamex College of Saint Adeline";

$update_success = null;
$update_error = null;

// Check if announcement_id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $announcement_id = intval($_GET['id']);

    // Fetch announcement details for the provided announcement_id
    $sql = "SELECT * FROM announcements WHERE announcement_id = $announcement_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $announcement = $result->fetch_assoc(); // Fetch announcement data
    } else {
        die("Announcement not found.");
    }
} else {
    die("Invalid request. Announcement ID is missing.");
}

// Handle form submission for updating announcement data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $title = sanitize_input($conn, $_POST["reg_title"]);
    $content = sanitize_input($conn, $_POST["reg_content"]);
    $announcement_start_date = sanitize_input($conn, $_POST["announcement_start_date"]);
    $announcement_end_date = sanitize_input($conn, $_POST["announcement_end_date"]);

    if (!$update_error) {
        // Update query
        $sql = "UPDATE announcements 
                SET title = '$title', 
                    content = '$content',
                    start_date = '$announcement_start_date',
                    end_date = '$announcement_end_date' 
                    WHERE announcement_id = $announcement_id";

        if ($conn->query($sql) === TRUE) {
            $update_success = "Announcement updated successfully!";
        } else {
            $update_error = "Error updating announcement: " . $conn->error;
        }
    }
}

?>

<?php include('../../layouts/header.php'); ?>

<h2>Edit Announcement</h2><hr><br>

<?php if ($update_success) { echo "<p style='color: green;'>$update_success</p>"; } ?>
<?php if ($update_error) { echo "<p style='color: red;'>$update_error</p>"; } ?>

<form method="post" action="">
    <div class="deparment-form-container">
        <div class="one-col-form-group">
            <label for="reg_title">Title:</label>
            <input type="text" name="reg_title" id="reg_title" value="<?php echo $announcement['title']; ?>" required>
        </div>
        <div class="one-col-form-group">
            <label for="reg_content">Announcement Content:</label>
            <textarea name="reg_content" id="reg_content" rows="4" cols="50"><?php echo $announcement['content']; ?></textarea>
        </div>
        <div class="one-col-form-group">
            <label for="announcement_start_date">Start Date:</label>
            <input type="date" id="announcement_start_date" name="announcement_start_date" required>
        </div>
        <div class="one-col-form-group">
            <label for="announcement_end_date">End Date:</label>
            <input type="date" id="announcement_end_date" name="announcement_end_date" required>
        </div>
        <div class="one-col-form-group full-width">
            <button type="submit" name="update" class="btn_submit">
                <i class="fa-solid fa-save"></i> Update Announcement
            </button>
        </div>
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>
