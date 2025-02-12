<?php 
    $page_title = "Admin Dashboard";
    include_once('../../database/conn.php');
    include_once('../../layouts/header.php');

    $success = null;
    $error = null;

    // save course request
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_room"])) {
        $room_name = sanitize_input($conn, $_POST["room_name"]);
        $capacity = sanitize_input($conn, $_POST["capacity"]);

        $sql = "INSERT INTO rooms (room_name, capacity) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if($stmt) {
            // bind the param
            mysqli_stmt_bind_param($stmt, "ss", $room_name, $capacity);

            // execute the statement
            if(mysqli_stmt_execute($stmt)) {
                $success = "Room created successfully!";
            } else {
                $error = "Error creating room: " . mysqli_error($conn);
            }
        }
    }

?>

<h2>Room</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-container">
        <div class="form-group">
            <label for="room_name">Room Name:</label>
            <input type="text" id="room_name" name="room_name" required><br>
        </div>
        <div class="form-group">
            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" required><br>
        </div>
    </div>
    <button type="submit" name="create_room" id="create_room" class="btn_submit">
        <i class="fa-solid fa-floppy-disk"></i> Create Room
    </button>

</form>

<?php include('../../layouts/footer.php'); ?>