<?php
include('../../database/conn.php');
$page_title = "Edit Room - Datamex College of Saint Adeline";

$update_success = null;
$update_error = null;

// Check if room_id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $room_id = intval($_GET['id']);

    // Fetch room details for the provided room_id
    $sql = "SELECT * FROM rooms WHERE room_id = $room_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc(); // Fetch room data
    } else {
        die("Room not found.");
    }
} else {
    die("Invalid request. Room ID is missing.");
}

// Handle form submission for updating room data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $room_name = sanitize_input($conn, $_POST["reg_room_name"]);
    $capacity = sanitize_input($conn, $_POST["reg_capacity"]);

    if (!$update_error) {
        // Update query
        $sql = "UPDATE rooms 
                SET room_name = '$room_name', 
                    capacity = '$capacity'
                    WHERE room_id = $room_id";

        if ($conn->query($sql) === TRUE) {
            $update_success = "Room updated successfully!";
        } else {
            $update_error = "Error updating room: " . $conn->error;
        }
    }
}

?>

<?php include('../../layouts/header.php'); ?>

<h2>Edit Room</h2><hr><br>

<?php if ($update_success) { echo "<p style='color: green;'>$update_success</p>"; } ?>
<?php if ($update_error) { echo "<p style='color: red;'>$update_error</p>"; } ?>

<form method="post" action="">
    <div class="form-container">
        <div class="form-group">
            <label for="reg_room_name">Room Name:</label>
            <input type="text" name="reg_room_name" id="reg_room_name" value="<?php echo $room['room_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_capacity">Room Code:</label>
            <input type="number" name="reg_capacity" id="reg_capacity" value="<?php echo $room['capacity']; ?>" required>
        </div>
        <div class="form-group full-width">
            <button type="submit" name="update" class="btn_submit">
                <i class="fa-solid fa-save"></i> Update Room
            </button>
        </div>
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>
